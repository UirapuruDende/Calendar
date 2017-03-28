<?php
namespace Dende\Calendar\Domain\Calendar;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\SoftDeleteable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * Class Event.
 */
class Event
{
    use SoftDeleteable;

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

    /**
     * @var ArrayCollection|Occurrence[]
     */
    protected $occurrences;

    /**
     * @var DateTime[]
     */
    protected $occurrencesDates;

    /**
     * Event constructor.
     *
     * @param string                       $id
     * @param EventType                    $type
     * @param DateTime                     $startDate
     * @param DateTime                     $endDate
     * @param string                       $title
     * @param Repetitions                  $repetitions
     * @param ArrayCollection|Occurrence[] $occurrences
     *
     * @throws \Exception
     */
    public function __construct($id, EventType $type, DateTime $startDate, DateTime $endDate, $title, Repetitions $repetitions, ArrayCollection $occurrences = null)
    {
        if (Carbon::instance($startDate)->gt(Carbon::instance($endDate))) {
            throw new \Exception(sprintf(
                "End date '%s' cannot be before start date '%s'",
                $endDate->format('Y-m-d H:i:s'),
                $startDate->format('Y-m-d H:i:s')
            ));
        }

        $this->id = $id;
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->title = $title;
        $this->repetitions = $repetitions;
        $this->duration = Duration::calculate($this->startDate(), $this->endDate());
        $this->occurrences = $occurrences ?: new ArrayCollection([]);
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
     *
     * @return \DateTime[]|ArrayCollection
     */
    public function calculateOccurrencesDates($force = false)
    {
        if (is_null($this->occurrencesDates) || !$force) {
            $occurrences = new ArrayCollection();

            if ($this->isSingle()) {
                $occurrences->add($this->startDate);
            } elseif ($this->isWeekly()) {
                $interval = new DateInterval('P1D');
                /** @var DateTime[] $period */
                $period = new DatePeriod($this->startDate, $interval, $this->endDate);
                foreach ($period as $date) {
                    if (in_array($date->format('N'), $this->repetitions->weekdays())) {
                        $occurrences->add($date);
                    }
                }
            } else {
                throw new Exception('Invalid event type!');
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
     * @param Repetitions $repetitions
     */
    public function changeRepetitions(Repetitions $repetitions)
    {
        $this->repetitions = $repetitions;
        $this->calculateOccurrencesDates(true);
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
     * @param UpdateEventCommand $command
     */
    public function updateWithCommand(UpdateEventCommand $command)
    {
        $this->startDate = $command->startDate;
        $this->endDate = $command->endDate;
        $this->duration = Duration::calculate($this->startDate(), $this->endDate());
        $this->title = $command->title;
        $this->repetitions = new Repetitions($command->repetitionDays);
    }

    /**
     * @param DateTime $date
     */
    public function closeAtDate(DateTime $date)
    {
        foreach($this->occurrences() as $occurrence) {
            if ($occurrence->endDate() > $date) {
                $occurrence->setDeletedAt(new DateTime());
            }
        }

        $this->endDate = $date;
    }

    /**
     * @param OccurrenceFactoryInterface $factory
     */
    public function generateOccurrenceCollection(OccurrenceFactoryInterface $factory)
    {
        $this->occurrences = new ArrayCollection();

        foreach ($this->calculateOccurrencesDates() as $date) {
            $occurrences->add($factory->createFromArray([
                'startDate' => $date,
                'duration'  => $this->duration()->minutes(),
                'event'     => $this,
            ]));
        }
    }
}
