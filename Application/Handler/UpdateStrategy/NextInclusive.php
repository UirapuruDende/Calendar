<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class NextInclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     */
    public function update(UpdateEventCommandInterface $command)
    {
        $occurrence    = $this->occurrenceRepository->findOneById($command->occurrenceId);
        $originalEvent = $this->eventRepository->findOneByOccurrence($occurrence);

        if ($originalEvent->isSingle()) {
            throw new Exception('This strategy is for series types events!');
        }

        $pivotDate = $this->findPivotDate($occurrence, $originalEvent);
        $originalEvent->closeAtDate($pivotDate);

        foreach ($originalEvent->occurrences() as $occurrence) {
            $this->occurrenceRepository->update($occurrence);
        }

        if ($command instanceof UpdateEventCommand) {
            $calendar = $originalEvent->calendar();
            $eventId  = EventId::create();
            $calendar->addEvent($eventId, $command->title, $pivotDate, $command->endDate, $originalEvent->type(), new Repetitions($command->repetitions));
            $newEvent = $calendar->getEventById($eventId);
            $this->eventRepository->insert($newEvent);
        }

        $this->eventRepository->update($originalEvent);
    }

    /**
     * @param OccurrenceInterface $occurrence
     * @param Event               $event
     *
     * @return DateTime
     *
     * @internal param UpdateEventCommand $command
     */
    public function findPivotDate(OccurrenceInterface $editedOccurrence, Event $event) : DateTime
    {
        /** @var ArrayCollection|Occurrence[] $occurrences */
        $occurrences = $event->occurrences();

        /** @var ArrayCollection $filteredOccurrencesBeforeEdited */
        $filteredOccurrencesBeforeEdited = $occurrences->filter(function (Occurrence $occurrence) use ($editedOccurrence) {
            return $occurrence->endDate() <= $editedOccurrence->startDate();
        });

        $iterator = $filteredOccurrencesBeforeEdited->getIterator();

        $iterator->uasort(function (Occurrence $a, Occurrence $b) {
            return $a->startDate() > $b->startDate();
        });

        if ($latestOccurrence = end($iterator)) {
            return $latestOccurrence->endDate();
        }

        return $editedOccurrence->endDate();
    }
}
