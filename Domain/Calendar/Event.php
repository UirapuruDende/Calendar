<?php
namespace Dende\Calendar\Domain\Calendar;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventData;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\SoftDeleteable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Event.
 */
class Event implements EventInterface
{
    use SoftDeleteable;

    const DUMP_FORMAT = 'd.m H.i';

    /** @var UuidInterface */
    protected $id;

    /**
     * @var Calendar
     */
    protected $calendar;

    /**
     * @var EventType
     */
    protected $type;

    /**
     * @var Duration
     */
    protected $duration;

    /**
     * @var ArrayCollection|OccurrenceInterface[]
     */
    protected $occurrences;

    /**
     * @var EventData
     */
    protected $eventData;

    public function __construct(UuidInterface $eventId, Calendar $calendar, EventType $type, DateTime $startDate, DateTime $endDate, string $title, Repetitions $repetitions = null, ArrayCollection $occurrences = null)
    {
        if (Carbon::instance($startDate)->gte(Carbon::instance($endDate))) {
            throw new Exception(sprintf(
                "End date '%s' cannot be before start date '%s'",
                $endDate->format('Y-m-d H:i:s'),
                $startDate->format('Y-m-d H:i:s')
            ));
        }

        $duration = Duration::calculate($startDate, $endDate);

        if ($type->isSingle()) {
            $endDate = (clone $startDate)->modify(sprintf('+ %d minutes', $duration->minutes()));

            if ($startDate->format('Ymd') !== $endDate->format('Ymd')) {
                throw new Exception('Single event should finish at the same day');
            }
        }

        if ($type->isWeekly() && (null === $repetitions || count($repetitions->getArray()) === 0)) {
            throw new Exception('Weekly repeated event must have at least one repetition');
        }

        $this->duration    = $duration;
        $this->id     = $eventId ?? Uuid::uuid4();
        $this->calendar    = $calendar;
        $this->type        = $type;
        $this->eventData   = new EventData($startDate, $endDate, $title, $repetitions ?: new Repetitions());
        $this->occurrences = $occurrences;

        if (null === $occurrences) {
            $this->occurrences = new ArrayCollection();
            $this->regenerateOccurrences();
        }
    }

    public static function create(UuidInterface $eventId, string $title, DateTime $startDate, DateTime $endDate, EventType $type, Repetitions $repetitions, Calendar $calendar, ?Collection $occurrences = null) : self
    {
        return new self($eventId, $calendar, $type,$startDate, $endDate,  $title, $repetitions,  $occurrences);
    }

    /**
     * @return ArrayCollection|OccurrenceInterface[]
     */
    public function occurrences() : Collection
    {
        return $this->occurrences;
    }

    public function title() : string
    {
        return $this->eventData->title();
    }

    public function type() : EventType
    {
        return $this->type;
    }

    public function repetitions() : Repetitions
    {
        return $this->eventData->repetitions();
    }

    public function duration() : Duration
    {
        if (null === $this->duration) {
            $this->duration = Duration::calculate($this->eventData->startDate(), $this->eventData->endDate());
        }

        return $this->duration;
    }

    public function startDate() : DateTime
    {
        return $this->eventData->startDate();
    }

    public function endDate() : DateTime
    {
        return $this->eventData->endDate();
    }

    public function move(DateTime $startDate) : void
    {
        throw new Exception('Implement me');
    }

    public function isSingle() : bool
    {
        return $this->type()->isSingle();
    }

    public function isWeekly() : bool
    {
        return $this->type()->isWeekly();
    }

    public function closeAtDate(DateTime $date)
    {
        $this->resize(null, $date, null);
    }

    public function resize(DateTime $newStartDate = null, DateTime $newEndDate = null, Repetitions $repetitions = null, OccurrenceInterface $occurrence = null)
    {
        $this->eventData = $this->eventData->update($newStartDate, $newEndDate, $this->eventData->title(), $repetitions);

        if (null === $occurrence) {
            $this->regenerateOccurrences();
        } else {
            $this->regenerateOccurrences($this->findPivotDate($occurrence));
        }
    }

    protected function regenerateOccurrences(DateTime $pivotDate = null)
    {
        if ($this->isSingle()) {
            $this->occurrences->clear();

            $this->occurrences->add(Occurrence::create(null, $this->eventData->startDate(), $this));

            return;
        }

        if (null !== $pivotDate && ($pivotDate < $this->eventData->startDate() || $pivotDate > $this->eventData->endDate())) {
            throw new Exception(
                sprintf(
                    'Pivot (%s) must be between startDate (%s) and endDate (%s)!',
                    $pivotDate->format('Y/m/d H:i:s'),
                    $this->eventData->startDate()->format('Y/m/d H:i:s'),
                    $this->eventData->endDate()->format('Y/m/d H:i:s')
                )
            );
        }

        $oldCollection = new ArrayCollection($this->occurrences->toArray());

        if (null === $pivotDate) {
            $this->occurrences->clear();
            $pivotDate = $this->eventData->startDate();
        } else {
            $pivotDate = Carbon::instance($pivotDate)->subMinutes($this->duration()->minutes())->addDays(1);

            $oldCollection = new ArrayCollection($this->occurrences->toArray());
            $oldCollection = $oldCollection->filter(function (OccurrenceInterface $occurrence) use ($pivotDate) {
                return $occurrence->startDate() >= $pivotDate;
            });
            $this->occurrences = $this->occurrences->filter(function (OccurrenceInterface $occurrence) use ($pivotDate) {
                return $occurrence->endDate() < $pivotDate;
            });
        }

        $tmpCollection = new ArrayCollection();

        $endDate = $this->endDate();

        $period = new DatePeriod($pivotDate, new DateInterval('P1D'), $endDate);

        /** @var DateTime $date */
        foreach ($period as $date) {
            if (!in_array((int) $date->format('N'), $this->eventData->repetitions()->getArray(), false)) {
                continue;
            }

            $tmpCollection->add(Occurrence::create(null,  $date, $this));
        }

        /** @var OccurrenceInterface[]|ArrayCollection $paddedCollection */
        $paddedCollection = $oldCollection->filter(function (OccurrenceInterface $occurrence) use ($pivotDate, $endDate) {
            return $pivotDate <= $occurrence->startDate() && $occurrence->endDate() <= $endDate;
        });

        foreach ($tmpCollection as $newOccurrence) {
            foreach ($paddedCollection as $oldOccurrence) {
                if ($newOccurrence->startDate()->format('Ymd') === $oldOccurrence->startDate()->format('Ymd')) {
                    $oldOccurrence->synchronizeWithEvent();
                    $this->occurrences->add($oldOccurrence);

                    continue 2;
                }
            }

            $this->occurrences->add($newOccurrence);
        }
    }

    public function getOccurrenceById(UuidInterface $occurrenceId) : OccurrenceInterface
    {
        $result = $this->occurrences()->filter(function (OccurrenceInterface $occurrence) use ($occurrenceId) {
            return $occurrence->id()->equals($occurrenceId);
        });

        return $result->first();
    }

    public function id() : UuidInterface
    {
        return $this->id;
    }

    public function calendar() : Calendar
    {
        return $this->calendar;
    }

    /**
     * @codeCoverageIgnore
     */
    public function dumpDatesAsString() : string
    {
        return sprintf('[%s - %s]', $this->eventData->startDate()->format(self::DUMP_FORMAT), $this->eventData->endDate()->format(self::DUMP_FORMAT));
    }

    /**
     * @codeCoverageIgnore
     */
    public function dumpOccurrencesDatesAsString() : string
    {
        $array = $this->occurrences()->map(function (OccurrenceInterface $occurrence) {
            return sprintf('[%s:%s]', $occurrence->startDate()->format(self::DUMP_FORMAT), $occurrence->duration()->minutes());
        });

        return implode(' ', $array->getValues());
    }

    public function findPivotDate(OccurrenceInterface $editedOccurrence) : DateTime
    {
        /** @var ArrayCollection $filteredOccurrencesBeforeEdited */
        $filteredOccurrencesBeforeEdited = $this->occurrences->filter(function (OccurrenceInterface $occurrence) use ($editedOccurrence) {
            return $occurrence->endDate() <= $editedOccurrence->startDate();
        });

        $iterator = $filteredOccurrencesBeforeEdited->getIterator();

        $iterator->uasort(function (OccurrenceInterface $a, OccurrenceInterface $b) {
            return $a->startDate() > $b->startDate();
        });

        if ($latestOccurrence = end($iterator)) {
            return $latestOccurrence->endDate();
        }

        return $editedOccurrence->endDate();
    }

    public function update(EventData $data)
    {
        $this->eventData = $data;
        $this->duration  = Duration::calculate($this->startDate(), $this->endDate());

        if ($this->isSingle()) {
            $this->occurrences->first()->synchronizeWithEvent();
        }
    }

    public static function setFactoryClass(string $class)
    {
        if (!class_exists($class)) {
            throw new Exception(sprintf('Class %s does not exist', $class));
        }

        self::$occurrenceFactoryClass = $class;
    }
}
