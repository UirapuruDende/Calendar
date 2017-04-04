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

        $pivotDate = $originalEvent->findPivotDate($occurrence);
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
}
