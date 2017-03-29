<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use Carbon\Carbon;
use DateInterval;
use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Dende\Calendar\Domain\SoftDeleteable;

/**
 * Class Occurrence.
 */
class Occurrence implements OccurrenceInterface
{
    use SoftDeleteable;

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
     * @var string|OccurrenceId
     */
    protected $id;

    /**
     * Occurrence constructor.
     *
     * @param string             $id
     * @param DateTime           $startDate
     * @param OccurrenceDuration $duration
     */
    public function __construct($id, DateTime $startDate, OccurrenceDuration $duration)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->duration = $duration;
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
        $this->modified = true;
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
        $diff = new DateInterval(sprintf('PT%dM', abs($this->duration()->minutes())));
        $endDate->add($diff);

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

    /**
     * @return string
     */
    public function id() : string
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

    public function synchronizeWithEvent(Event $event)
    {
        if ($event->isSingle()) {
            $this->changeStartDate($event->startDate());
        } elseif ($event->isWeekly()) {
            $this->startDate->modify($event->startDate()->format('H:i:s'));
        }

        $this->changeDuration(new OccurrenceDuration($event->duration()->minutes()));
    }
}
