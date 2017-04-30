<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory;

use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class InMemoryEventRepository implements EventRepositoryInterface
{
    /**
     * @var Event[]|ArrayCollection
     */
    private $events;

    /**
     * InMemoryEventRepository constructor.
     *
     * @param Event[]|ArrayCollection $events
     */
    public function __construct(ArrayCollection $events = null)
    {
        $this->events = $events ?: new ArrayCollection();
    }

    /**
     * @return Event[]|Collection
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
        $this->events[$event->id()->__toString()] = $event;
    }

    /**
     * @param Event $event
     */
    public function update(Event $event)
    {
        $this->events[$event->id()->__toString()] = $event;
    }

    /**
     * @param Event $event
     */
    public function remove(Event $event)
    {
        unset($this->events[$event->id()->__toString()]);
    }

    public function findOneByOccurrence(Occurrence $occurrence) : Event
    {
        $events = array_filter($this->events->getIterator()->getArrayCopy(), function (Event $event) use ($occurrence) {
            return $event->occurrences()->contains($occurrence);
        });

        return array_pop($events);
    }

    public function findOneById(string $id)
    {
        return $this->events[$id];
    }
}
