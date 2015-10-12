<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;

/**
 * Class Single
 * @package Dende\Calendar\Application\Handler\UpdateStrategy
 */
final class Single implements UpdateStrategyInterface
{
    use SetRepositoriesTrait;

    /**
     * @param UpdateEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommand $command)
    {
        $occurrence = $command->occurrence;
        $event = $occurrence->event();

        if ($event->isType(EventType::TYPE_SINGLE)) {
            $event->changeStartDate($command->startDate);
            $event->changeEndDate($command->endDate);
            $event->changeDuration(new Duration($command->duration));
            $event->changeTitle($command->title);

            $occurrence->changeStartDate($command->startDate);
            $occurrence->changeDuration(new Duration($command->duration));
        }
        if ($event->isType(EventType::TYPE_WEEKLY)) {
            $occurrence->changeStartDate($command->startDate);
            $occurrence->changeDuration(new Duration($command->duration));
        }

        $this->eventRepository->update($event);
        $this->occurrenceRepository->update($occurrence);
    }
}
