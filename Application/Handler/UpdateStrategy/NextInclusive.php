<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
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

        if($originalEvent->type()->isType(Event\EventType::TYPE_SINGLE)) {
            $originalEvent->updateWithCommand($command);
            $originalEvent->occurrences()->first()->synchronizeWithEvent();
        } elseif($originalEvent->type()->isType(Event\EventType::TYPE_WEEKLY)) {
            $originalEvent->changeEndDate($command->occurrence->startDate());
            $pivot = $this->findPivotDate($command);

            $originalOccurrences = $originalEvent->occurrences()->map(function(Occurrence $occurrence) use ($pivot) {
                if($occurrence->endDate() >= $pivot) {
                    $occurrence->setDeletedAt(new DateTime());
                }

                return $occurrence;
            });

            $originalEvent->setOccurrences($originalOccurrences);

            $newCommand = clone($command);
            $newCommand->startDate = $pivot;

            /** @var Event $newEvent */
            $newEvent = $this->eventFactory->createFromCommand($newCommand);
            $newOccurrences = $this->occurrenceFactory->generateCollectionFromEvent($newEvent);
            $newEvent->setOccurrences($newOccurrences);

            $this->eventRepository->insert($newEvent);
            $this->occurrenceRepository->update($originalEvent->occurrences());
        }

        $this->eventRepository->update($originalEvent);
    }

    /**
     * @param UpdateEventCommand $command
     * @return DateTime
     */
    private function findPivotDate(UpdateEventCommand $command)
    {
        /** @var ArrayCollection|Occurrence[] $occurrences */
        $occurrences = $command->occurrence->event()->occurrences();

        /** @var Occurrence $occurrence */
        $clicked = $command->occurrence;

        /** @var ArrayCollection $earlier */
        $earlier = $occurrences->filter(function(Occurrence $occurrence) use ($clicked) {
            return $occurrence->endDate() <= $clicked->startDate();
        });

        return $earlier->last()->endDate();
    }
}
