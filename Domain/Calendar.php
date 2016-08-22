<?php
namespace Dende\Calendar\Domain;

use DateTime;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Calendar
 * @package Gyman\Domain\Domain
 */
class Calendar
{
    use SoftDeleteable;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ArrayCollection|Event[]
     */
    protected $events;

    /**
     * @var DateTime|null
     */
    protected $deletedAt;

    /**
     * @param string $id
     * @param string $name
     */
    public function __construct($id = null, $name = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->events = new ArrayCollection();
    }

    /**
     * @param Event $event
     */
    public function insertEvent(Event $event)
    {
        $this->events->add($event);
        $event->assignCalendar($this);
    }

    /**
     * @return ArrayCollection|Event[]
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    public function updateName($title)
    {
        $this->name = $title;
    }
}
