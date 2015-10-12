<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommand;
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
     * @return mixed
     */
    public function setEventRepository(EventRepositoryInterface $eventRepository);

    /**
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     * @return mixed
     */
    public function setOccurrenceRepository(OccurrenceRepositoryInterface $occurrenceRepository);
}
