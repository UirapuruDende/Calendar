<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration as OccurrenceDuration;

/**
 * Class Single.
 */
final class Single implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     */
    public function update(UpdateEventCommandInterface $command)
    {
        $occurrence = $command->occurrence;

        $event = $occurrence->event();

        if ($command instanceof RemoveEventCommand) {
            if ($event->isType(EventType::TYPE_SINGLE)) {
                $this->eventRepository->remove($event);
            }
            $this->occurrenceRepository->remove($occurrence);

            return;
        }

        if ($event->isSingle()) {
            $event->updateWithCommand($command);
            $occurrence->synchronizeWithEvent();
            $this->occurrenceRepository->update($occurrence);
        } elseif ($event->isWeekly()) {
            $occurrence->changeStartDate($command->startDate);
            $occurrence->changeDuration(new OccurrenceDuration($command->duration));
            $this->occurrenceRepository->update($occurrence);
        }

        $this->eventRepository->update($event);
    }
}
