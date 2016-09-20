<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Domain\Calendar\Event\Duration as EventDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\Duration as OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\EventType;

/**
 * Class Single
 * @package Dende\Calendar\Application\Handler\UpdateStrategy
 */
final class Single implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @todo: crate and update to DurationFactory
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommandInterface $command)
    {
        $occurrence = $command->occurrence;

        $event = $occurrence->event();

        if ($event->isType(EventType::TYPE_SINGLE)) {
            $event->changeStartDate($command->startDate);
            $event->changeEndDate($command->endDate);
            $event->changeDuration(new EventDuration($command->duration));
            $event->changeTitle($command->title);

            $occurrence->changeStartDate($command->startDate);
            $occurrence->changeDuration(new OccurrenceDuration($command->duration));
        } else if ($event->isType(EventType::TYPE_WEEKLY)) {
            $occurrence->changeStartDate($command->startDate);
            $occurrence->changeDuration(new OccurrenceDuration($command->duration));
        }

        $this->eventRepository->update($event);
        $this->occurrenceRepository->update($occurrence);
    }
}
