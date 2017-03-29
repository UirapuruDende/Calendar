<?php
namespace Dende\Calendar\Domain;

use Dende\Calendar\Application\Factory\EventFactory;
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
     * @var string
     */
    static public $eventFactoryClass = EventFactory::class;

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

    public function createEvent(string $title, EventType $type, DateTime $startDate, DateTime $endDate, Repetitions $repetitions = null)
    {
        $this->events->add((self::$eventFactoryClass)::createFromArray([
            "startDate" => $startDate,
            "endDate" => $endDate,
            "type" => $type,
            "repetitions" => $repetitions,
            "title" => $title
        ]));
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
