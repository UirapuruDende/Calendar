<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use DateInterval;
use DateTime;

trait CalculateDurationTrait
{
    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     *
     * @return self
     */
    public static function calculate(DateTime $startDate, DateTime $endDate) : self
    {
        /** @var DateTime $tmpEndDate */
        $tmpEndDate = clone $endDate;
        $tmpEndDate->modify($startDate->format('Y-m-d'));

        // in situation of duration from i.e. 23:55 - 0:05, to avoid inverted diff, we add one day
        if ($tmpEndDate < $startDate) {
            $tmpEndDate->modify('+1 day');
        }

        /** @var DateInterval */
        $diff    = $startDate->diff($tmpEndDate);
        $minutes = $diff->h * 60 + $diff->i;

        return new self($minutes);
    }
}
