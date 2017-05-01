<?php
namespace Dende\Calendar\Application\Service\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateOccurrenceCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Exception;

/**
 * Class AllInclusive.
 *
 * @property OccurrenceRepositoryInterface occurrenceRepository
 * @property EventRepositoryInterface eventRepository
 */
final class Overwrite implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateOccurrenceCommand|RemoveEventCommand $command
     */
    public function update(UpdateOccurrenceCommand $command)
    {
        throw new Exception('Implement me');
    }
}
