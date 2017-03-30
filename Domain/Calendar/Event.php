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
    public function __construct(IdInterface $eventId, Calendar $calendar, EventType $type, DateTime $startDate, DateTime $endDate, string $title, Repetitions $repetitions, ArrayCollection $occurrences = null)
    {
        if (Carbon::instance($startDate)->gte(Carbon::instance($endDate))) {
            throw new Exception(sprintf(
                "End date '%s' cannot be before start date '%s'",
                $endDate->format('Y-m-d H:i:s'),
                $startDate->format('Y-m-d H:i:s')
            ));
        }

        if ($type->isWeekly() && count($repetitions->getArray()) === 0) {
            throw new Exception('Weekly repeated event must have at least one repetition');
        }

        $this->eventId     = $eventId;
        $this->calendar    = $calendar;
        $this->type        = $type;
        $this->startDate   = $startDate;
        $this->endDate     = $endDate;
        $this->title       = $title;
        $this->repetitions = $repetitions;
        $this->duration    = Duration::calculate($this->startDate(), $this->endDate());
        $this->occurrences = $occurrences ?: new ArrayCollection();

        if (0 === $this->occurrences()->count()) {
            $this->generateOccurrenceCollection();
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
     * @param Duration $duration
     */
//    public function resize(Duration $duration)
//    {
//        $this->duration = $duration;
//        $this->resetAllOccurrences();
//    }

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

    public function isType(string $type) : bool
    {
        return $this->type()->isType($type);
    }

    public function isSingle() : bool
    {
        return $this->isType(EventType::TYPE_SINGLE);
    }

    public function isWeekly() : bool
    {
        return $this->isType(EventType::TYPE_WEEKLY);
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
    public function closeAtDate(DateTime $date, DateTime $closingDate = null)
    {
        foreach ($this->occurrences as $occurrence) {
            if ($occurrence->endDate() > $date) {
                $occurrence->setDeletedAt($closingDate ?: new DateTime());
            }
        }

        $this->endDate = $date;
    }

    protected function generateOccurrenceCollection()
    {
        /** @var OccurrenceFactoryInterface $factory */
        $factory           = new self::$occurrenceFactoryClass();
        $this->occurrences = new ArrayCollection();

        $add = function (DateTime $date) use ($factory) {
            $this->occurrences->add($factory->createFromArray([
                'startDate' => $date,
                'duration'  => new OccurrenceDuration($this->duration()->minutes()),
                'event'     => $this,
            ]));
        };

        if ($this->isSingle()) {
            $add($this->startDate);
        } elseif ($this->isWeekly()) {
            $interval = new DateInterval('P1D');
            /** @var DateTime[] $period */
            $period = new DatePeriod($this->startDate, $interval, $this->endDate);

            foreach ($period as $date) {
                if (in_array($date->format('N'), $this->repetitions->getArray())) {
                    $add($date);
                }
            }
        }
    }

    public function id() : IdInterface
    {
        return $this->eventId;
    }

    public function calendar() : Calendar
    {
        return $this->calendar;
    }

    public function dumpDatesAsString() : string
    {
        return sprintf('[%s-%s-%s]', $this->startDate()->format(self::DUMP_FORMAT), $this->endDate()->format(self::DUMP_FORMAT), $this->getDeletedAt() ? $this->getDeletedAt()->format(self::DUMP_FORMAT) : '_');
    }

    public function dumpOccurrencesDatesAsString() : string
    {
        $array = $this->occurrences()->map(function (Occurrence $occurrence) {
            return sprintf('[%s:%s:%s]', $occurrence->startDate()->format(self::DUMP_FORMAT), $occurrence->duration()->minutes(), $occurrence->getDeletedAt() ? $occurrence->getDeletedAt()->format(self::DUMP_FORMAT) : '_');
        });

        return implode(' ', $array->getValues());
    }
}
