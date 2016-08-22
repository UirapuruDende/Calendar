<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Handler\UpdateStrategy\UpdateStrategyInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Exception;

/**
 * Class RemoveEventHandler
 * @package Dende\Calendar\Application\Handler
 */
class RemoveEventHandler
{
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var OccurrenceRepositoryInterface
     */
    private $occurrenceRepository;

    /**
     * RemoveEventHandler constructor.
     * @param EventRepositoryInterface $eventRepository
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     */
    public function __construct(EventRepositoryInterface $eventRepository, OccurrenceRepositoryInterface $occurrenceRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->occurrenceRepository = $occurrenceRepository;
    }

    /**
     * @param Event $event
     */
    public function remove(Event $event)
    {
        $this->occurrenceRepository->remove($event->occurrences());
        $this->eventRepository->remove($event);
    }

    /**
     * @param Event[] $events
     */
    public function removeCollection($events = [])
    {
        foreach($events as $event)
        {
            $this->remove($event);
        }
    }
}
