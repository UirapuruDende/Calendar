<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;

class NextInclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommand $command)
    {
        /** @var Event $originalEvent */
        $originalEvent = $command->occurrence->event();
        $originalEvent->changeEndDate($command->occurrence->startDate());
        $pivot = $command->occurrence->startDate();

        $filteredCollection = $originalEvent->occurrences()->filter(function(Occurrence $occurrence) use ($pivot) {
            return $occurrence->endDate() < $pivot;
        });

        $originalEvent->setOccurrences($filteredCollection);

        $newCommand = clone($command);
        $newCommand->startDate = $pivot;

        /** @var Event $newEvent */
        $newEvent = $this->eventFactory->createFromCommand($newCommand);
        $newOccurrences = $this->occurrenceFactory->generateCollectionFromEvent($newEvent);
        $newEvent->setOccurrences($newOccurrences);

        $this->eventRepository->update($originalEvent);
        $this->eventRepository->insert($newEvent);
    }
}
