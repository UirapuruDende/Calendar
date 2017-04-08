<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use DateTime;

class EventData
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
     * @var string
     */
    protected $title;

    /**
     * @var Repetitions
     */
    protected $repetitions;

    /**
     * EventData constructor.
     *
     * @param DateTime    $startDate
     * @param DateTime    $endDate
     * @param string      $title
     * @param Repetitions $repetitions
     */
    public function __construct(DateTime $startDate, DateTime $endDate, $title, Repetitions $repetitions)
    {
        $this->startDate   = $startDate;
        $this->endDate     = $endDate;
        $this->title       = $title;
        $this->repetitions = $repetitions;
    }

    public function update(DateTime $newStartDate = null, DateTime $newEndDate = null, string $title = null, Repetitions $repetitions = null)
    {
        return new self($newStartDate ?: $this->startDate, $newEndDate ?: $this->endDate, $title ?: $this->title, $repetitions ?: $this->repetitions());
    }

    public function title() : string
    {
        return $this->title;
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
        return $this->endDate;
    }

    /**
     * @return Repetitions
     */
    public function repetitions(): Repetitions
    {
        return $this->repetitions;
    }
}
