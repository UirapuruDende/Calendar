<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use DateTime;

/**
 * Class Duration.
 */
class Duration
{
    /**
     * @var int
     */
    protected $minutes;

    /**
     * Duration constructor.
     *
     * @param int $minutes
     */
    public function __construct(int $minutes)
    {
        $this->minutes = $minutes;
    }

    public function minutes() : int
    {
        return $this->minutes;
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     *
     * @return Duration
     */
    public static function calculate(DateTime $startDate, DateTime $endDate) : Duration
    {
        /** @var DateTime $tmpEndDate */
        $tmpEndDate = clone $endDate;
        $tmpEndDate->modify($startDate->format('Y-m-d'));

        // in situation of duration from i.e. 23:55 - 0:05, to avoid inverted diff, we add one day
        if ($tmpEndDate < $startDate) {
            $tmpEndDate->modify('+1 day');
        }

        /** @var DateInterval */
        $diff = $startDate->diff($tmpEndDate);

        return new self($diff->h * 60 + $diff->i);
    }
}
