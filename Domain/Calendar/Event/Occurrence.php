<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceData;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Dende\Calendar\Domain\Calendar\EventInterface;
use Dende\Calendar\Domain\IdInterface;
use Exception;

/**
 * Class Occurrence.
 */
class Occurrence implements OccurrenceInterface
{
    /**
     * Doctrine id.
     * @var int
     */
    protected $id;

    /**
     * @var OccurrenceId
     */
    protected $occurrenceId;

    /**
     * @var OccurrenceData
     */
    protected $occurrenceData;

    /**
     * @var bool
     */
    protected $modified = false;

    /**
     * @var Event
     */
    protected $event;

    /**
     * Occurrence constructor.
     *
     * @param OccurrenceId|IdInterface $occurrenceId
     * @param Event                    $event
     * @param DateTime                 $startDate
     * @param OccurrenceDuration       $duration
     *
     * @throws Exception
     */
    public function __construct(IdInterface $occurrenceId = null, EventInterface $event, DateTime $startDate = null, OccurrenceDuration $duration = null)
    {
        $this->occurrenceId = $occurrenceId ?: OccurrenceId::create();
        $this->event        = $event;

        if (null === $this->event) {
            throw new Exception('Event has to be set!');
        }

        if (null === $startDate) {
            $this->occurrenceData = OccurrenceData::createFromEvent($event);
        } elseif (null === $duration) {
            $this->occurrenceData = new OccurrenceData($startDate, new OccurrenceDuration($event->duration()->minutes()));
        } else {
            $this->occurrenceData = new OccurrenceData($startDate, $duration);
        }
    }

    public function update(OccurrenceData $data)
    {
        if ($data->startDate()->format('Ymd') !== $this->occurrenceData->startDate()->format('Ymd')) {
            throw new Exception(sprintf(
                "You can't change a day of occurrence, only hour! (new: %s vs old: %s)",
                $data->startDate()->format('Y-m-d H:i:s'),
                $this->occurrenceData->startDate()->format('Y-m-d H:i:s')
            ));
        }

        $this->occurrenceData = $data;
    }

    /**
     * @param OccurrenceDuration $newDuration
     */
    public function resize(DurationInterface $newDuration)
    {
        $this->occurrenceData = $this->occurrenceData->updateDuration($newDuration);
    }

    /**
     * @param DateTime $startDate
     */
    public function move(DateTime $startDate)
    {
        if ($startDate->format('Ymd') !== $this->occurrenceData->startDate()->format('Ymd')) {
            throw new Exception("You can't change a day of occurrence, only hour!");
        }

        $this->occurrenceData = $this->occurrenceData->updateStartDate($startDate);
    }

    /**
     * @return bool
     */
    public function isOngoing() : bool
    {
        return Carbon::now()->between(Carbon::instance($this->startDate()), Carbon::instance($this->endDate()));
    }

    /**
     * @return bool
     */
    public function isPast() : bool
    {
        return Carbon::now()->greaterThan(Carbon::instance($this->endDate()));
    }

    /**
     * @return DateTime
     */
    public function startDate() : DateTime
    {
        return $this->occurrenceData->startDate();
    }

    /**
     * @return OccurrenceDuration
     */
    public function duration() : DurationInterface
    {
        return $this->occurrenceData->duration();
    }

    /**
     * @return DateTime
     */
    public function endDate() : DateTime
    {
        return $this->occurrenceData->endDate();
    }

    public function id() : IdInterface
    {
        return $this->occurrenceId;
    }

    protected function setAsModified()
    {
        $this->modified = true;
    }

    public function isModified() : bool
    {
        return $this->modified;
    }

    public function synchronizeWithEvent()
    {
        if ($this->event->isSingle()) {
            $this->move($this->event->startDate());
            $this->resize($this->event->duration());
        } elseif ($this->event->isWeekly()) {
            $newStartDate = $this->occurrenceData->startDate();
            $newStartDate->modify($this->event->startDate()->format('H:i:s'));
            $this->move($newStartDate);
        }

        $this->modified = false;
    }

    public function event() : Event
    {
        return $this->event;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function dumpDatesAsString() : string
    {
        return sprintf('[%s:%s]', $this->startDate()->format('d/m'), $this->duration()->minutes());
    }
}
