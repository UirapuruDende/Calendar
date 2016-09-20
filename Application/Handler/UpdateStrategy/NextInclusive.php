<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Doctrine\Common\Collections\ArrayCollection;

class NextInclusive implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommandInterface $command)
    {
        /** @var Event $originalEvent */
        $originalEvent = $command->occurrence->event();

        if($originalEvent->type()->isType(Event\EventType::TYPE_SINGLE)) {
            $originalEvent->updateWithCommand($command);

            /** @var Occurrence $occurrence */
            $occurrence = $originalEvent->occurrences()->first();

            /**
             * @todo this should not be here! it's a Single strategy responsibility!
             */
            if($command->type === Event\EventType::TYPE_SINGLE) {
                $occurrence->synchronizeWithEvent();
                $this->occurrenceRepository->update($occurrence);
            } elseif ($command->type === Event\EventType::TYPE_WEEKLY) {
                $this->occurrenceRepository->remove($occurrence);

                $occurrences = $this->occurrenceFactory->generateCollectionFromEvent($originalEvent);
                $originalEvent->setOccurrences($occurrences);
                $this->occurrenceRepository->insert($occurrences);
            }

        } elseif($originalEvent->type()->isType(Event\EventType::TYPE_WEEKLY)) {
            $originalEvent->changeEndDate($command->occurrence->startDate());
            $pivot = $this->findPivotDate($command->occurrence);

            $originalOccurrences = $originalEvent->occurrences()->map(function(Occurrence $occurrence) use ($pivot) {
                if($occurrence->endDate() > $pivot) {
                    $occurrence->setDeletedAt(new DateTime());
                }

                return $occurrence;
            });

            /** @var Event $nextEvent */
            $nextEvent = $originalEvent->next();

            while(!is_null($nextEvent)) {
                $nextEvent->occurrences()->map(function(Occurrence $occurrence) {
                    $occurrence->setDeletedAt(new DateTime());
                    return $occurrence;
                });
                $nextEvent->setDeletedAt(new DateTime());
                $nextEvent->unsetPrevious();
                $nextEvent = $nextEvent->next();
            }

            $originalEvent->setOccurrences($originalOccurrences);

            if($command instanceof UpdateEventCommand) {
                $newCommand = clone($command);
                $newCommand->startDate = $pivot;

                /** @var Event $newEvent */
                $newEvent = $this->eventFactory->createFromCommand($newCommand);
                $newOccurrences = $this->occurrenceFactory->generateCollectionFromEvent($newEvent);
                $newEvent->setOccurrences($newOccurrences);

                $originalEvent->setNext($newEvent);
                $this->eventRepository->insert($newEvent);
            }

            $this->occurrenceRepository->update($originalEvent->occurrences());
        }

        $this->eventRepository->update($originalEvent);
    }

    /**
     * @param UpdateEventCommand $command
     * @return DateTime
     */
    public function findPivotDate(Occurrence $clicked)
    {
        /** @var ArrayCollection|Occurrence[] $occurrences */
        $occurrences = $clicked->event()->occurrences();

        /** @var ArrayCollection $beforeClicked */
        $beforeClicked = $occurrences->filter(function(Occurrence $occurrence) use ($clicked) {
            return $occurrence->endDate() <= $clicked->startDate();
        });

        $iterator = $beforeClicked->getIterator();

        $iterator->uasort(function(Occurrence $a, Occurrence $b){
            return $a->startDate() > $b->startDate();
        });

        if($latestOccurrence = end($iterator)) {
            return $latestOccurrence->endDate();
        } else {
            return $clicked->endDate();
        }
    }

}
