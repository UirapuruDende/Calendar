<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\Specification\InMemoryEventSpecificationInterface;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryEventsByDateRangeAndCalendarSpecification;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryEventsByTitleSpecification;
use Exception;

class InMemoryEventRepository implements EventRepositoryInterface
{
    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * @return Event[]
     */
    public function findAll()
    {
        return $this->events;
    }

    /**
     * @param Event $event
     */
    public function insert(Event $event)
    {
        $this->events[$event->id()] = $event;
    }

    /**
     * @param InMemoryEventSpecificationInterface $specification
     *
     * @return array
     */
    public function query(InMemoryEventSpecificationInterface $specification)
    {
        return $this->filterEvents(
            function (Event $event) use ($specification) {
                return $specification->specifies($event);
            }
        );
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    private function filterEvents(callable $callback)
    {
        $result = array_values(array_filter($this->events, $callback));

        usort($result, function (Event $a, Event $b) {
            return $a->startDate() < $b->startDate() ? -1 : 1;
        });

        return $result;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $calendar
     *
     * @return array
     */
    public function findAllByCalendarInDateRange(DateTime $startDate, DateTime $endDate, Calendar $calendar)
    {
        return $this->query(
            new InMemoryEventsByDateRangeAndCalendarSpecification($startDate, $endDate, $calendar)
        );
    }

    /**
     * @param $title
     * @param Calendar|null $calendar
     *
     * @return array
     */
    public function findOneByTitle($title, Calendar $calendar = null)
    {
        return $this->query(
            new InMemoryEventsByTitleSpecification($title, $calendar)
        );
    }

    /**
     * @param Event $event
     */
    public function update(Event $event)
    {
        if (!isset($this->events[$event->id()])) {
            throw new Exception(sprintf('Event with id %s is not set, cannot update!', $event->id()));
        }

        $this->events[$event->id()] = $event;
    }

    /**
     * @param Event $event
     */
    public function remove(Event $event)
    {
        unset($this->events[$event->id()]);
    }
}
