<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Doctrine\Common\Collections\ArrayCollection;

class CalendarFactory implements CalendarFactoryInterface
{
    public function createFromArray(array $array = []) : Calendar
    {
        $template = [
            'calendarId' => CalendarId::create(),
            'title'      => '',
            'events'     => [],
        ];

        $array = array_merge($template, $array);

        return new Calendar(
            $array['calendarId'],
            $array['title'],
            new ArrayCollection($array['events'])
        );
    }

    public function create() : Calendar
    {
        return $this->createFromArray([]);
    }
}
