<?php
namespace Dende\Calendar\Domain\Calendar;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\IdInterface;
use Dende\Calendar\Domain\SoftDeleteable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * Class Event.
 */
class Event
{
    use SoftDeleteable;

    const DUMP_FORMAT = 'd.m H.i';

    public static $occurrenceFactoryClass = OccurrenceFactory::class;

    /**
     * Id for Doctrine.
     *
     * @var int
     */
    protected $id;

    /**
     * @var EventId
     */
    protected $eventId;

    /**
     * @var Calendar
     */
    protected $calendar;

    /**
     * @var EventType
     */
    protected $type;

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Repetitions
     */
    protected $repetitions;

    /**
     * @var Duration
     */
    protected $duration;

    /**
     * @var ArrayCollection|Occurrence[]
     */
    protected $occurrences;

    /**
     * Event constructor.
     *
     * @param EventId                      $eventId
     * @param Calendar                     $calendar
     * @param EventType                    $type
     * @param DateTime                     $startDate
     * @param DateTime                     $endDate
     * @param string                       $title
     * @param Repetitions                  $repetitions
     * @param ArrayCollection|Occurrence[] $occurrences
     *
     * @throws Exception
     *
     * @internal param string $id
     */
    public function __construct(IdInterface $eventId = null, Calendar $calendar, EventType $type, DateTime $startDate, DateTime $endDate, string $title, Repetitions $repetitions = null, ArrayCollection $occurrences = null)
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
        $this->startDate   = $startDate;
        $this->endDate     = $endDate;
        $this->eventId     = $eventId ?: EventId::create();
        $this->calendar    = $calendar;
        $this->type        = $type;
        $this->title       = $title;
        $this->repetitions = $repetitions;
        $this->occurrences = $occurrences;

        if (null === $occurrences) {
            $this->occurrences = new ArrayCollection();
            $this->regenerateOccurrences();
        }
    }

    /**
     * @return ArrayCollection|Occurrence[]
     */
    public function occurrences() : ArrayCollection
    {
        return $this->occurrences;
    }

    /**
     * @return string
     */
    public function title() : string
    {
        return $this->title;
    }

    /**
     * @return EventType
     */
    public function type() : EventType
    {
        return $this->type;
    }

    /**
     * @return Repetitions
     */
    public function repetitions() : Repetitions
    {
        return $this->repetitions;
    }

    /**
     * @return Duration
     */
    public function duration() : Duration
    {
        return $this->duration;
    }

    /**
     * @return DateTime
     */
    public function startDate() : DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function endDate() : DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime $startDate
     */
//    public function move(DateTime $startDate)
//    {
//        $this->startDate = $startDate;
//        $this->resetAllOccurrences();
//    }

    /**
     * @param $title
     */
    public function changeTitle(string $title)
    {
        $this->title = $title;
    }

    public function isSingle() : bool
    {
        return $this->type()->isSingle();
    }

    public function isWeekly() : bool
    {
        return $this->type()->isWeekly();
    }

    /**
     * @param Occurrence $occurrenceToRemove
     */
    public function removeOccurrence(Occurrence $occurrenceToRemove)
    {
        foreach ($this->occurrences() as $key => $occurrence) {
            if ($occurrence->id() === $occurrenceToRemove->id()) {
                $this->occurrences->remove($key);
                break;
            }
        }
    }

    /**
     * @param DateTime $date
     */
    public function closeAtDate(DateTime $date)
    {
        $this->resize(null, $date, null);
    }

    public function resize(DateTime $newStartDate = null, DateTime $newEndDate = null, Repetitions $repetitions = null, Occurrence $occurrence = null)
    {
        $this->startDate   = $newStartDate ?: $this->startDate;
        $this->endDate     = $newEndDate ?: $this->endDate;
        $this->repetitions = $repetitions ?: $this->repetitions;

        if (null === $occurrence) {
            $this->regenerateOccurrences();
        } else {
            $this->regenerateOccurrences($this->findPivotDate($occurrence));
        }
    }

    protected function regenerateOccurrences(DateTime $pivotDate = null)
    {
        if (null !== $pivotDate && ($pivotDate <= $this->startDate && $pivotDate >= $this->endDate)) {
            throw new Exception(
                sprintf(
                    'Pivot (%s) must be between startDate (%s) and endDate (%s)!',
                    $pivotDate->format('Y/m/d H:i:s'),
                    $this->startDate->format('Y/m/d H:i:s'),
                    $this->endDate->format('Y/m/d H:i:s')
                )
            );
        }

        /** @var OccurrenceFactoryInterface $factory */
        $factory = new self::$occurrenceFactoryClass();

        $oldCollection = new ArrayCollection($this->occurrences->toArray());

        if (null === $pivotDate) {
            $this->occurrences->clear();
            $pivotDate = $this->startDate();
        } else {
            $pivotDate = Carbon::instance($pivotDate)->subMinutes($this->duration()->minutes())->addDays(1);

            $oldCollection = new ArrayCollection($this->occurrences->toArray());
            $oldCollection = $oldCollection->filter(function (Occurrence $occurrence) use ($pivotDate) {
                return $occurrence->startDate() >= $pivotDate;
            });
            $this->occurrences = $this->occurrences->filter(function (Occurrence $occurrence) use ($pivotDate) {
                return $occurrence->endDate() < $pivotDate;
            });
        }

        $tmpCollection = new ArrayCollection();

        $endDate = $this->endDate();

        if ($this->isSingle()) {
            $this->occurrences->clear();

            $this->occurrences->add($factory->createFromArray([
              'startDate' => $this->startDate,
              'duration'  => new OccurrenceDuration($this->duration()->minutes()),
              'event'     => $this,
            ]));

            return;
        }

        $period = new DatePeriod($pivotDate, new DateInterval('P1D'), $this->endDate);

        /** @var DateTime $date */
        foreach ($period as $date) {
            if (in_array($date->format('N'), $this->repetitions->getArray())) {
                $occurrence = $factory->createFromArray([
                      'startDate' => $date,
                      'event'     => $this,
                ]);

                $tmpCollection->add($occurrence);
            }
        }

        /** @var OccurrenceInterface[]|ArrayCollection $paddedCollection */
        $paddedCollection = $oldCollection->filter(function (Occurrence $occurrence) use ($pivotDate, $endDate) {
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

    public function getOccurrenceById(IdInterface $occurrenceId) : Occurrence
    {
        $result = $this->occurrences()->filter(function (Occurrence $occurrence) use ($occurrenceId) {
            return $occurrence->id()->equals($occurrenceId);
        });

        return $result->first();
    }

    public function id() : IdInterface
    {
        return $this->eventId;
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
        return sprintf('[%s-%s-%s]', $this->startDate()->format(self::DUMP_FORMAT), $this->endDate()->format(self::DUMP_FORMAT), $this->getDeletedAt() ? $this->getDeletedAt()->format(self::DUMP_FORMAT) : '_');
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

        $iterator->uasort(function (Occurrence $a, Occurrence $b) {
            return $a->startDate() > $b->startDate();
        });

        if ($latestOccurrence = end($iterator)) {
            return $latestOccurrence->endDate();
        }

        return $editedOccurrence->endDate();
    }
}
