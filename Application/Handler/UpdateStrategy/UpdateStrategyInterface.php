<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;

/**
 * Interface UpdateStrategyInterface.
 */
interface UpdateStrategyInterface
{
    /**
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     */
    public function update(UpdateEventCommandInterface $command);

    /**
     * @param EventRepositoryInterface $eventRepository
     */
    public function setEventRepository(EventRepositoryInterface $eventRepository);

    /**
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     */
    public function setOccurrenceRepository(OccurrenceRepositoryInterface $occurrenceRepository);

    /**
     * @param EventFactory $eventFactory
     */
    public function setEventFactory(EventFactoryInterface $eventFactory);

    /**
     * @param OccurrenceFactory $occurrenceFactory
     */
    public function setOccurrenceFactory(OccurrenceFactoryInterface $occurrenceFactory);
}
