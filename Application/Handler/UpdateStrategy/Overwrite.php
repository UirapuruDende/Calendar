<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

/**
 * Class AllInclusive
 * @package Dende\Calendar\Application\Handler\UpdateStrategy
 * @property OccurrenceRepositoryInterface|OccurrenceRepositoryInterface occurrenceRepository
 * @property EventRepositoryInterface eventRepository
 */
final class Overwrite implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @todo add fabrics for value objects or update commands
     * @param UpdateEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommand $command)
    {
        /** @var Event $event */
        $event = $command->occurrence->event();

        $event->changeDuration(new Duration($command->duration));
        $event->changeStartDate($command->startDate);
        $event->changeEndDate($command->endDate);
        $event->changeTitle($command->title);
        $event->changeType(new EventType($command->type));
        $event->changeRepetitions(new Repetitions($command->repetitionDays));

        if($command->calendar->id() != $event->calendar()->id()) {
            $event->changeCalendar($command->calendar);
        }

        $this->occurrenceRepository->removeAllForEvent($event);

        $occurrences = $this->occurrenceFactory->generateCollectionFromEvent($event);
        $event->setOccurrences($occurrences);

        $this->eventRepository->update($event);
    }
}
