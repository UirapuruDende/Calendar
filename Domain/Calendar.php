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
use Doctrine\Common\Collections\Collection;

/**
 * Class Calendar.
 */
class Calendar
{
    use SoftDeleteable;

    /**
     * Doctrine id.
     *
     * @var int
     */
    protected $id;

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
    public function __construct(CalendarId $calendarId = null, string $title = '', Collection $events = null)
    {
        $this->calendarId = $calendarId ?: CalendarId::create();
        $this->title      = $title;
        $this->events     = $events ?: new ArrayCollection();
    }

    public static function create(string $title = '') : Calendar
    {
        return new static(CalendarId::create(), $title);
    }

    public function addEvent(EventId $eventId, string $title, DateTime $startDate, DateTime $endDate, EventType $type, Repetitions $repetitions = null, Collection $occurrences = null)
    {
        /** @var EventFactoryInterface $factory */
        $factory = new self::$eventFactoryClass();

        $event = $factory->createFromArray([
            'eventId'     => $eventId,
            'title'       => $title,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'type'        => $type,
            'repetitions' => $repetitions,
            'calendar'    => $this,
            'occurrences' => $occurrences,
        ]);

        $this->events->add($event);
    }

    /**
     * @return Collection|Event[]
     */
    public function events() : Collection
    {
        return $this->events;
    }

    public function id() : CalendarId
    {
        return $this->calendarId;
    }

    public function title() : string
    {
        return $this->title;
    }

    public function getEventById(EventId $eventId) : Event
    {
        $result = $this->events()->filter(function (Event $event) use ($eventId) {
            return $event->id()->equals($eventId);
        });

        return $result->first();
    }
}
