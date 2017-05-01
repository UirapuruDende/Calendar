<?php
namespace Dende\Calendar\Application\Service\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;

/**
 * Class AllInclusive.
 *
 * @property OccurrenceRepositoryInterface occurrenceRepository
 * @property EventRepositoryInterface eventRepository
 */
final class AllInclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface $command
     */
    public function update(UpdateEventCommandInterface $command)
    {
    }
}
