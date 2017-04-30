<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateCommand;
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
     * @param UpdateEventCommandInterface|UpdateCommand|RemoveEventCommand $command
     */
    public function update(UpdateCommand $command)
    {
        throw new Exception('Implement me');
    }
}
