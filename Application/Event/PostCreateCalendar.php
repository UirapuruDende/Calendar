<?php
namespace Dende\Calendar\Application\Event;

use Dende\Calendar\Domain\Calendar;
use Symfony\Component\EventDispatcher\Event;

class PostCreateCalendar extends Event
{
    protected $calendar;

    /**
     * PostCreateCalendar constructor.
     *
     * @param $calendar
     */
    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }
}
