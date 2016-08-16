<?php
namespace Dende\Calendar\Domain\Calendar;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * Class Event
 * @package Gyman\Domain\Model
 */
class Event
{
    /**
     * @var string
     */
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

    /** @var ArrayCollection|Occurrence[] */
    protected $occurrences;

    /**
     * @var DateTime[]
     */
    protected $occurrencesDates;

    /**
     * @var Event
     */
    protected $previousEvent;

    /**
     * Event constructor.
     * @param string $id
     * @param Calendar $calendar
     * @param EventType $type
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string $title
     * @param Repetitions $repetitions
     * @param Duration $duration
     * @param ArrayCollection|Occurrence[] $occurrences
     * @throws \Exception
     */
    public function __construct($id, Calendar $calendar, EventType $type, DateTime $startDate, DateTime $endDate, $title, Repetitions $repetitions, Duration $duration, Event $previousEvent=null)
    {
        if (Carbon::instance($startDate)->gt(Carbon::instance($endDate))) {
            throw new \Exception(sprintf(
                "End date '%s' cannot be before start date '%s'",
                $endDate->format("Y-m-d H:i:s"),
                $startDate->format("Y-m-d H:i:s")
            ));
        }

        $calendar->insertEvent($this);

        $this->id = $id;
        $this->calendar = $calendar;
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->title = $title;
        $this->repetitions = $repetitions;
        $this->duration = $duration;
        $this->previousEvent = $previousEvent;
    }

    /**
     * @return ArrayCollection|Occurrence[]
     */
    public function occurrences()
    {
        return $this->occurrences;
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @return EventType
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @return Repetitions
     */
    public function repetitions()
    {
        return $this->repetitions;
    }

    /**
     * @return Duration
     */
    public function duration()
    {
        return $this->duration;
    }

    /**
     * @return Calendar
     */
    public function calendar()
    {
        return $this->calendar;
    }

    /**
     * @return DateTime
     */
    public function startDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function endDate()
    {
        return $this->endDate;
    }

    /**
     * @param bool $force
     * @return \DateTime[]|ArrayCollection
     */
    public function calculateOccurrencesDates($force = false)
    {
        if (is_null($this->occurrencesDates) || !$force) {
            $occurrences = new ArrayCollection();

            if ($this->type->isType(EventType::TYPE_SINGLE)) {
                $occurrences->add($this->startDate);
            } elseif ($this->type->isType(EventType::TYPE_WEEKLY)) {
                $interval = new DateInterval('P1D');
                /** @var DateTime[] $period */
                $period = new DatePeriod($this->startDate, $interval, $this->endDate);
                foreach ($period as $date) {
                    if (in_array($date->format('N'), $this->repetitions->weekly())) {
                        $occurrences->add($date);
                    }
                }
            } else {
                throw new Exception("Invalid event type!");
            }

            $this->occurrencesDates = $occurrences;
        }

        return $this->occurrencesDates;
    }

    /**
     * @param Repetitions $repetitions
     */
//    public function updateRepetitions(Repetitions $repetitions)
//    {
//        $this->repetitions = $repetitions;
//        $this->resetAllOccurrences();
//    }

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

    public function assignCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param ArrayCollection $occurrences
     */
    public function setOccurrences(ArrayCollection $occurrences)
    {
        $this->occurrences = $occurrences;
    }

    /**
     * @param DateTime $startDate
     */
    public function changeStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @param DateTime $endDate
     */
    public function changeEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @param Duration $duration
     */
    public function changeDuration(Duration $duration)
    {
        $this->duration = $duration;
    }

    /**
     * @param $title
     */
    public function changeTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isType($type)
    {
        return $this->type()->isType($type);
    }

    /**
     * @param Repetitions $repetitions
     */
    public function changeRepetitions(Repetitions $repetitions)
    {
        $this->repetitions = $repetitions;
        $this->calculateOccurrencesDates(true);
    }

    /**
     * @param EventType $type
     * @throws Exception
     */
    public function changeType(EventType $type)
    {
        $this->type = $type;
        $this->calculateOccurrencesDates(true);
    }

    /**
     * @param Calendar $calendar
     */
    public function changeCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @param Occurrence $occurrenceToRemove
     */
    public function removeOccurrence(Occurrence $occurrenceToRemove)
    {
        foreach($this->occurrences() as $key => $occurrence) {
            if($occurrence->id() === $occurrenceToRemove->id()) {
                $this->occurrences->remove($key);
                break;
            }
        }
    }

    /**
     * @return Event
     */
    public function previous()
    {
        return $this->previousEvent;
    }
}
