<?php
namespace Dende\Calendar\Application\Command;

use Dende\Calendar\Domain\Calendar\CalendarId;

final class RemoveCalendarCommand
{
    /**
     * @var CalendarId
     */
    public $calendarId;
}
