<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

/**
 * Class AllExclusive.
 *
 * @property OccurrenceRepositoryInterface occurrenceRepository
 * @property EventRepositoryInterface eventRepository
 */
final class AllExclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     */
    public function update(UpdateEventCommandInterface $command)
    {
        $occurrence = $command->occurrence;

        /** @var Event $event */
        $event = $occurrence->event();

        if ($event->isType(EventType::TYPE_SINGLE)) {
            throw new \Exception('Implement this!');
        }
        if ($event->isType(EventType::TYPE_WEEKLY)) {
            $event->changeStartDate($command->startDate);
            $event->changeEndDate($command->endDate);
            $event->changeDuration(new Duration($command->duration));
            $event->changeTitle($command->title);
            $event->changeRepetitions(new Repetitions($command->repetitions));

            foreach ($event->occurrences() as $occurrence) {
                if ($occurrence->isModified()) {
                    continue;
                }

                $event->occurrences()->removeElement($occurrence);
            }
        }

        $this->eventRepository->update($event);
    }
}
