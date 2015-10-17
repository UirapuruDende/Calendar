<?php
namespace Dende\Calendar\Domain\Calendar;

/**
 * Class CalendarId
 * @package Dende\Calendar\Model
 */
final class CalendarId
{
    /**
     * @var string
     */
    private $id;

    /**
     * CalendarId constructor.
     * @param $id
     */
    public function __construct($id = null)
    {
        if (is_null($id)) {
            $id = uniqid('calendar_');
        }

        $this->id = $id;
    }

    /**
     * @return string
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

    /**
     * @return string
     */
    function __toString()
    {
        return (string) $this->id();
    }
}
