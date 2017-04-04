<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use Carbon\Carbon;
use DateInterval;
use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Dende\Calendar\Domain\IdInterface;
use Dende\Calendar\Domain\SoftDeleteable;
use Exception;

/**
 * Class Occurrence.
 */
class Occurrence implements OccurrenceInterface
{
    /**
     * Doctrine id.
     *
     * @var int
     */
    protected $id;

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
     * @var bool
     */
    protected $modified = false;

    /**
     * @var OccurrenceId
     */
    protected $occurrenceId;

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
     */
    public function __construct(IdInterface $occurrenceId, Event $event, DateTime $startDate, OccurrenceDuration $duration)
    {
        $this->occurrenceId = $occurrenceId;
        $this->event        = $event;
        $this->startDate    = $startDate;
        $this->duration     = $duration;
        $this->updateEndDate();
    }

    /**
     * @param OccurrenceDuration $newDuration
     */
    public function resize(OccurrenceDuration $newDuration)
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
        $this->modified  = true;
        $this->startDate = $newStartDate;
        $this->updateEndDate();
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

    protected function updateEndDate()
    {
        $endDate = clone $this->startDate();
        $diff    = new DateInterval(sprintf('PT%dM', abs($this->duration()->minutes())));
        $endDate->add($diff);

        if ($this->startDate()->format('Ymd') !== $endDate->format('Ymd')) {
            new Exception(sprintf("Event occurrence can't overlap to new day (start: %s end: %s)", $this->startDate()->format('Y.m.d H:i:s'), $endDate->format('Y.m.d H:i:s')));
        }

        $this->endDate = $endDate;
    }

    /**
     * @return DateTime
     */
    public function startDate() : DateTime
    {
        return $this->startDate;
    }

    /**
     * @return OccurrenceDuration
     */
    public function duration() : OccurrenceDuration
    {
        return $this->duration;
    }

    /**
     * @return DateTime
     */
    public function endDate() : DateTime
    {
        if (is_null($this->endDate)) {
            $this->updateEndDate();
        }

        return $this->endDate;
    }

    public function id() : IdInterface
    {
        return $this->occurrenceId;
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
     * @param OccurrenceDuration $duration
     */
    public function changeDuration(OccurrenceDuration $duration)
    {
        $this->duration = $duration;
        $this->updateEndDate();
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
        if ($this->event()->isSingle()) {
            $this->changeStartDate($this->event()->startDate());
        } elseif ($this->event()->isWeekly()) {
            $this->startDate->modify($this->event()->startDate()->format('H:i:s'));
        }

        $this->changeDuration(new OccurrenceDuration($this->event()->duration()->minutes()));
        $this->modified = false;
    }

    public function event() : Event
    {
        return $this->event;
    }

    public function dumpDatesAsString() : string
    {
        return sprintf('[%s:%s:%s]', $this->startDate()->format('d/m'), $this->duration()->minutes(), $this->getDeletedAt() ? $this->getDeletedAt()->format('d/m') : '_');
    }
}
