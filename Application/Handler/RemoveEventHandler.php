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
final class RemoveEventHandler
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
     * CreateEventHandler constructor.
     * @param EventRepositoryInterface $eventRepository
     */
    public function __construct(
        EventRepositoryInterface $eventRepository,
        OccurrenceRepositoryInterface $occurrenceRepository
    )
    {
        $this->eventRepository = $eventRepository;
        $this->occurrenceRepository = $occurrenceRepository;
    }

    /**
     * @param UpdateEventCommand $command
     */
    public function remove(Event $event)
    {
        $this->occurrenceRepository->removeAllForEvent($event);
        $this->eventRepository->remove($event);
    }
}
