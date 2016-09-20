<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;

class NextExclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommandInterface $command)
    {
        // TODO: Implement update() method.
    }
}
