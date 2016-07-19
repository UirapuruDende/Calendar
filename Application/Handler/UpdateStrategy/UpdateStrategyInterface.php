<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

/**
 * Interface UpdateStrategyInterface
 * @package Dende\Calendar\Application\Handler\UpdateStrategy
 */
interface UpdateStrategyInterface
{
    /**
     * @param UpdateEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommand $command);

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
