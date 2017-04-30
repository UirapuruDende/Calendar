<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Exception;

class NextInclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateCommand|RemoveEventCommand $command
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

        if ($command instanceof UpdateCommand) {
            $calendar = $originalEvent->calendar();
            $eventId  = EventId::create();
            $calendar->addEvent($eventId, $command->title, $pivotDate, $command->endDate, $originalEvent->type(), new Repetitions($command->repetitions));
            $newEvent = $calendar->getEventById($eventId);
            $this->eventRepository->insert($newEvent);
        }

        $this->eventRepository->update($originalEvent);
    }
}
