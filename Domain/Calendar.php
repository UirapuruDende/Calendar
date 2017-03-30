<?php
namespace Dende\Calendar\Domain;

use DateTime;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Class Calendar.
 */
class Calendar
{
    use SoftDeleteable;

    /**
     * @var CalendarId
     */
    protected $id;

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
    static public $eventFactoryClass = EventFactory::class;

    /**
     * @param string $id
     * @param string $name
     */
    public function __construct(CalendarId $id, $name = '', ArrayCollection $events = null)
    {
        $this->id = $id;
        $this->title = $name;
        $this->events = $events ?: new ArrayCollection();
    }

    public function createEvent(string $title, EventType $type, DateTime $startDate, DateTime $endDate, Repetitions $repetitions = null)
    {
        /** @var Event $event */
        $event = (self::$eventFactoryClass)::createFromArray([
            "calendar" => $this,
            "startDate" => $startDate,
            "endDate" => $endDate,
            "type" => $type,
            "repetitions" => $repetitions,
            "title" => $title,
        ]);

        $event->generateOccurrenceCollection();

        $this->events->add($event);
    }

    public function resizeEvent(EventId $id, DateTime $startDate, DateTime $endDate)
    {
        $result = $this->events()->matching(Criteria::create()->where(
            Criteria::expr()->eq('id', $id)
        ));

        $result->first()->resize($startDate, $endDate);
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
    public function title()
    {
        return $this->title;
    }
}
