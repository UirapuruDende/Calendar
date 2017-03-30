<?php

namespace Dende\Calendar\Domain\Calendar;


class CalendarId
{
    /** @var string */
    private $id;

    /**
     * CalendarId constructor.
     * @param $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id() : string
    {
        return $this->id;
    }
}