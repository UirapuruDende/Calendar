<?php
namespace Dende\Calendar\Application\Event;

use Dende\Calendar\Domain\Calendar\Event as CalendarEvent;
use Symfony\Component\EventDispatcher\Event;

class PostUpdateEvent extends Event
{
    /** @var  CalendarEvent */
    private $event;

    /** @var string */
    private $occurrenceId;

    /** @var string */
    private $method;

    /**
     * EventPostUpdate constructor.
     * @param CalendarEvent $event
     * @param string $occurrenceId
     * @param string $method
     */
    public function __construct(CalendarEvent $event, string $occurrenceId, string $method)
    {
        $this->event = $event;
        $this->occurrenceId = $occurrenceId;
        $this->method = $method;
    }

    /**
     * @return CalendarEvent
     */
    public function getEvent(): CalendarEvent
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getOccurrenceId(): string
    {
        return $this->occurrenceId;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
