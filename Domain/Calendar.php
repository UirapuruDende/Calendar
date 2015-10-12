<?php
namespace Dende\Calendar\Domain;

use DateTime;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Class Calendar
 * @package Gyman\Domain\Domain
 */
final class Calendar
{
    /**
     * @var CalendarId
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ArrayCollection|Event[]
     */
    private $events;

    /**
     * @param CalendarId $id
     */
    public function __construct(CalendarId $id, $name = '')
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
     * @return CalendarId
     */
    public function id()
    {
        return $this->id;
    }

//    public function getCurrentEvent()
//    {
//        return $this->getEventsByDate(new DateTime('now'));
//    }

//    public function getEventsByDate(DateTime $date)
//    {
//        $expr = Criteria::expr();
//        $criteria = Criteria::create();
//
//        $criteria->where(
//            $expr->orX(
//                $expr->andX(
//                    $expr->eq('.type()', new EventType(EventType::TYPE_WEEKLY))
//                ),
//                $expr->andX(
//                    $expr->eq('type', new EventType(EventType::TYPE_SINGLE))
//                )
//            )
//        );
//
//        return $this->events()->matching($criteria);
//    }
}
