<?php
namespace Dende\Calendar\Application\Service\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateOccurrenceCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Interface UpdateStrategyInterface.
 */
interface UpdateStrategyInterface
{
    /**
     * @param UpdateOccurrenceCommand $command
     */
    public function update(UpdateOccurrenceCommand $command);

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

    /**
     * @param EventDispatcher $dispatcher
     */
    public function setEventDispatcher(EventDispatcher $dispatcher);
}
