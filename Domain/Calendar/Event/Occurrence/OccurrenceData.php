<?php
namespace Dende\Calendar\Domain\Calendar\Event\Occurrence;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Exception;

class OccurrenceData
{
    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @var OccurrenceDuration
     */
    protected $duration;

    /**
     * OccurrenceData constructor.
     *
     * @param DateTime           $startDate
     * @param DateTime           $endDate
     * @param OccurrenceDuration $duration
     */
    public function __construct(DateTime $startDate, OccurrenceDuration $duration)
    {
        $this->startDate = clone $startDate;
        $this->duration  = clone $duration;

        $this->calculateEndDate();
    }

    protected function calculateEndDate()
    {
        $endDate = Carbon::instance($this->startDate)->copy();
        $endDate->addMinutes($this->duration->minutes());

        if ($this->startDate->format('Ymd') !== $endDate->format('Ymd')) {
            new Exception(sprintf("Event occurrence can't overlap to a new day (start: %s end: %s)", $this->startDate->format('Y.m.d H:i:s'), $endDate->format('Y.m.d H:i:s')));
        }

        $this->endDate = $endDate;
    }

    /**
     * @return DateTime
     */
    public function startDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function endDate(): DateTime
    {
        if (null === $this->endDate) {
            $this->calculateEndDate();
        }

        return $this->endDate;
    }

    /**
     * @return OccurrenceDuration
     */
    public function duration(): OccurrenceDuration
    {
        return $this->duration;
    }

    public static function createFromEvent(Event $event) : self
    {
        if ($event->isSingle()) {
            return new self($event->startDate(), new OccurrenceDuration($event->duration()->minutes()));
        }

        throw new Exception('Can create only from Single type event!');
    }

    public function updateDuration(OccurrenceDuration $duration) : self
    {
        return new self($this->startDate, $duration);
    }

    public function updateStartDate(DateTime $startDate) : self
    {
        return new self($startDate, $this->duration);
    }
}
