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
use Dende\CalendarBundle\Repository\ORM\OccurrenceRepository;

/**
 * Class AllInclusive
 * @package Dende\Calendar\Application\Handler\UpdateStrategy
 * @property OccurrenceRepositoryInterface|OccurrenceRepository occurrenceRepository
 * @property EventRepositoryInterface eventRepository
 */
final class Overwrite implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
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

        foreach ($event->occurrences() as $occurrence) {
            $this->occurrenceRepository->remove($occurrence);
        }

        $occurrences = $this->occurrenceFactory->generateCollectionFromEvent($event);

        $this->occurrenceRepository->insertCollection($occurrences);
        $event->setOccurrences($occurrences);

        $this->eventRepository->update($event);
    }
}
