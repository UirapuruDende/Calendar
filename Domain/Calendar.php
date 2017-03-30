<?php
namespace Dende\Calendar\Domain;

use DateTime;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Calendar.
 */
class Calendar
{
    use SoftDeleteable;

    /**
     * @var CalendarId
     */
    protected $calendarId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var ArrayCollection|Event[]
     */
    protected $events;

    /**
     * @var string
     */
    public static $eventFactoryClass = EventFactory::class;

    /**
     * @param CalendarId|string    $calendarId
     * @param string               $title
     * @param ArrayCollection|null $events
     */
    public function __construct(IdInterface $calendarId, string $title = '', ArrayCollection $events = null)
    {
        $this->calendarId = $calendarId;
        $this->title      = $title;
        $this->events     = $events ?: new ArrayCollection();
    }

    public static function create(string $title = '') : Calendar
    {
        return new self(CalendarId::create(), $title);
    }

    public function addEvent(IdInterface $eventId, string $title, DateTime $startDate, DateTime $endDate, EventType $type, Repetitions $repetitions)
    {
        /** @var EventFactoryInterface $factory */
        $factory = new static::$eventFactoryClass();

        $event = $factory->createFromArray([
            'eventId'     => $eventId,
            'title'       => $title,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'type'        => $type,
            'repetitions' => $repetitions,
            'calendar'    => $this,
        ]);

        $this->events->add($event);
    }

    /**
     * @return ArrayCollection|Event[]
     */
    public function events() : ArrayCollection
    {
        return $this->events;
    }

    public function id() : IdInterface
    {
        return $this->calendarId;
    }

    /**
     * @return string
     */
    public function title() : string
    {
        return $this->title;
    }

    public function getEventById(IdInterface $eventId) : Event
    {
        $result = $this->events()->filter(function (Event $event) use ($eventId) {
            return $event->id()->equals($eventId);
        });

        return $result->first();
    }
}
