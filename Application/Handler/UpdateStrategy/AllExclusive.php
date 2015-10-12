<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

/**
 * Class AllExclusive
 * @package Dende\Calendar\Application\Handler\UpdateStrategy
 * @property OccurrenceRepositoryInterface occurrenceRepository
 * @property EventRepositoryInterface eventRepository
 */
final class AllExclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait;

    /**
     * @param UpdateEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommand $command)
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
            $event->changeRepetitions(new Repetitions($command->repetitionDays));

            foreach($event->occurrences() as $occurrence)
            {
                if($occurrence->isModified())
                {
                    continue;
                }

                $event->occurrences()->removeElement($occurrence);
            }

            $newOccurrences = OccurrenceFactory::generateCollectionFromEvent($event, true);

        }

        $this->eventRepository->update($event);
    }
}
