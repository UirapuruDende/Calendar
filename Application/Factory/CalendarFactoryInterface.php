<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Domain\Calendar;

interface CalendarFactoryInterface
{
    public function create() : Calendar;

    public function createFromArray(array $array = []) : Calendar;
}
