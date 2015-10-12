<?php
namespace Dende\Calendar\Domain\Calendar;

/**
 * Class CalendarId
 * @package Dende\Calendar\Model
 */
class CalendarId
{
    private $id;

    /**
     * CalendarId constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param CalendarId $id
     * @return bool
     */
    public function isEqual(CalendarId $id)
    {
        return $this->id === $id->id();
    }
}
