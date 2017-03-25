<?php
namespace Dende\Calendar\Application\Manager;

use Dende\Calendar\Application\Factory\CalendarFactoryInterface;
use Dende\Calendar\Domain\Repository\CalendarRepositoryInterface;

interface Calendars
{
    public function calendarRepository() : CalendarRepositoryInterface;
    public function calendarFactory() : CalendarFactoryInterface;
}
