<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommand;

class NextInclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommand $command)
    {
        // TODO: Implement update() method.
    }
}
