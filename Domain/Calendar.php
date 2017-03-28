<?php
namespace Dende\Calendar\Domain;

use Dende\Calendar\Domain\Calendar\Event;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Calendar.
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
     * @param string $id
     * @param string $name
     */
    public function __construct($id = null, $name = '', ArrayCollection $events = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->events = $events ?: new ArrayCollection();
    }

    /**
     * @param Event $event
     */
    public function insertEvent(Event $event)
    {
        $this->events->add($event);
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
