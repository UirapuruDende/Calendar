<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Domain\Calendar\Event\EventData;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceData;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Exception;

final class Single implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait, SetDispatcherTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     */
    public function update(UpdateEventCommand $command)
    {
        /** @var OccurrenceInterface $occurrence */
        $occurrence = $this->occurrenceRepository->findOneBy(['occurrenceId.id' => $command->occurrenceId()]);

        $event = $occurrence->event();

        if ($event->isSingle()) {
            $event->update(new EventData($command->startDate, $command->endDate, $command->title, new Repetitions($command->repetitions)));
        } elseif ($event->isWeekly()) {
            throw new Exception('Only single type event is acceptable for this strategy');
        }

        $this->eventRepository->update($event);
    }
}
