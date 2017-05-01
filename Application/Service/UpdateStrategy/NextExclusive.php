<?php
namespace Dende\Calendar\Application\Service\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateOccurrenceCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Exception;

class NextExclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateOccurrenceCommand|RemoveEventCommand $command
     */
    public function update(UpdateEventCommandInterface $command)
    {
        throw new Exception('Implement me');
    }
}
