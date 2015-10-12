<?php
namespace Dende\Calendar\Domain\Calendar\Event;

/**
 * Class EventId
 * @package Dende\Calendar\Domain\Calendar\Event
 */
class EventId
{
    /**
     * @var string
     */
    private $id;

    /**
     * EventId constructor.
     * @param $id
     */
    public function __construct($id = null)
    {
        if (is_null($id)) {
            $id = uniqid('event_');
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
}
