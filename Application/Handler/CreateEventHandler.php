<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Exception;

/**
 * Class CreateEventHandler
 * @package Gyman\Domain\Handler
 */
final class CreateEventHandler
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
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @var OccurrenceFactory
     */
    private $occurrenceFactory;

    /**
     * CreateEventHandler constructor.
     * @param EventRepositoryInterface $eventRepository
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     * @param EventFactory $eventFactory
     * @param OccurrenceFactory $occurrenceFactory
     */
    public function __construct(EventRepositoryInterface $eventRepository, OccurrenceRepositoryInterface $occurrenceRepository, EventFactory $eventFactory, OccurrenceFactory $occurrenceFactory)
    {
        $this->eventRepository = $eventRepository;
        $this->occurrenceRepository = $occurrenceRepository;
        $this->eventFactory = $eventFactory;
        $this->occurrenceFactory = $occurrenceFactory;
    }

    /**
     * @param CreateEventCommand $command
     */
    public function handle(CreateEventCommand $command)
    {
        $event = $this->eventFactory->createFromCommand($command);

        $this->eventRepository->insert($event);

        $occurrences = $this->occurrenceFactory->generateCollectionFromEvent($event);

        if (count($occurrences) === 0) {
            throw new Exception('Could not generate occurrences from event');
        }

        $event->setOccurrences($occurrences);

        foreach ($occurrences as $occurrence) {
            $this->occurrenceRepository->insert($occurrence);
        }
    }
}
