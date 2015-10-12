<?php
namespace Dende\Calendar\Tests;

use Carbon\Carbon;
use DateTime;

trait AssertDatesEqualTrait
{
    /**
     * @param string|DateTime|Carbon $dateActual
     * @param string|DateTime|Carbon $dateExpected
     */
    private function assertDatesEqual($dateActual, $dateExpected)
    {
        if (is_string($dateActual)) {
            $date1 = Carbon::parse($dateActual);
        } elseif ($dateActual instanceof \DateTime) {
            $date1 = Carbon::instance($dateActual);
        }

        if (is_string($dateExpected)) {
            $date2 = Carbon::parse($dateExpected);
        } elseif ($dateExpected instanceof \DateTime) {
            $date2 = Carbon::instance($dateExpected);
        }

        $this->assertTrue($date1->eq($date2), sprintf(
            "Failed asserting that actual date '%s' is equal expected '%s'",
            $date1->format('Y-m-d H:i:s'),
            $date2->format('Y-m-d H:i:s')
        ));
    }
}
