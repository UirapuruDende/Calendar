<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;

/**
 * Class Occurrence
 * @package Gyman\Domain\Model
 */
class Occurrence
{
    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var DateTime
     */
    private $endDate;

    /**
     * @var Duration
     */
    private $duration;

    /**
     * @var bool
     */
    private $modified = false;

    /**
     * @var Event
     */
    private $event;

    /**
     * @var string
     */
    private $id;

    /**
     * Occurrence constructor.
     * @param string $id
     * @param DateTime $startDate
     * @param Duration $duration
     * @param Event $event
     */
    public function __construct($id, DateTime $startDate, Duration $duration, Event $event)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->duration = $duration;
        $this->event = $event;
        $this->updateEndDate();
    }

    /**
     * @param Duration $newDuration
     */
    public function resize(Duration $newDuration)
    {
        $this->modified = true;
        $this->duration = $newDuration;
        $this->updateEndDate();
    }

    /**
     * @param DateTime $newStartDate
     */
    public function move(DateTime $newStartDate)
    {
        $this->modified = true;
        $this->startDate = $newStartDate;
        $this->updateEndDate();
    }

    /**
     * @return bool
     */
    public function isOngoing()
    {
        return Carbon::now()->between(Carbon::instance($this->startDate()), Carbon::instance($this->endDate()));
    }

    /**
     * @return bool
     */
    public function isPast()
    {
        return Carbon::now()->greaterThan(Carbon::instance($this->endDate()));
    }

    private function updateEndDate()
    {
        $endDate = clone($this->startDate());
        $diff = new \DateInterval(sprintf('PT%dM', abs($this->duration()->minutes())));
        $endDate->add($diff);

        $this->endDate = $endDate;
    }

    /**
     * @return DateTime
     */
    public function startDate()
    {
        return $this->startDate;
    }

    /**
     * @return Duration
     */
    public function duration()
    {
        return $this->duration;
    }

    /**
     * @return Event
     */
    public function event()
    {
        return $this->event;
    }

    public function resetToEvent()
    {
        $this->modified = false;
        $this->startDate = '';
        $this->duration = $this->event->duration();
    }

    /**
     * @return DateTime
     */
    public function endDate()
    {
        if (is_null($this->endDate)) {
            $this->updateEndDate();
        }

        return $this->endDate;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param DateTime $startDate
     */
    public function changeStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;
        $this->setAsModified();
    }

    /**
     * @param Duration $duration
     */
    public function changeDuration(Duration $duration)
    {
        $this->duration = $duration;
        $this->setAsModified();
    }

    private function setAsModified()
    {
        $this->modified = true;
    }

    public function isModified()
    {
        return $this->modified;
    }
}
