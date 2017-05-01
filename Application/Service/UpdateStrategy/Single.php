<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateCommand;
use Dende\Calendar\Application\Service\UpdateStrategy\SetDispatcherTrait;
use Dende\Calendar\Application\Service\UpdateStrategy\SetFactoriesTrait;
use Dende\Calendar\Application\Service\UpdateStrategy\SetRepositoriesTrait;
use Dende\Calendar\Application\Service\UpdateStrategy\UpdateStrategyInterface;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceData;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;

final class Single implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait, SetDispatcherTrait;

    /**
     * @param UpdateCommand $command
     */
    public function update(UpdateCommand $command)
    {
        $occurrence = $this->occurrenceRepository->findOneById($command->occurrenceId());
        $occurrence->update(new OccurrenceData($command->startDate, OccurrenceDuration::calculate($command->startDate, $command->endDate)));
        $this->occurrenceRepository->update($occurrence);
    }
}
