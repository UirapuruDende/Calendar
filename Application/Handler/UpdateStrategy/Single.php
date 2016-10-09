<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration as OccurrenceDuration;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Single.
 */
final class Single implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @todo: crate and update to DurationFactory
     *
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     */
    public function update(UpdateEventCommandInterface $command)
    {
        $occurrence = $command->occurrence;

        $event = $occurrence->event();

        if ($command instanceof RemoveEventCommand) {
            if ($event->isType(EventType::TYPE_SINGLE)) {
                $this->eventRepository->remove($event);
            }
            $this->occurrenceRepository->remove($occurrence);
        } elseif ($command instanceof UpdateEventCommand) {
            if ($event->isType(EventType::TYPE_SINGLE)) {
                $event->updateWithCommand($command);

                if ($command->type === EventType::TYPE_WEEKLY) {
                    /** @var ArrayCollection|Occurrence[] $occurrences */
                    $occurrences = $this->occurrenceFactory->generateCollectionFromEvent($event);

                    $event->setOccurrences($occurrences);
                    $this->occurrenceRepository->remove($occurrence);
                    $this->occurrenceRepository->insert($occurrences);
                } elseif ($command->type === EventType::TYPE_SINGLE) {
                    $occurrence->synchronizeWithEvent();
                    $this->occurrenceRepository->update($occurrence);
                }
            } elseif ($event->isType(EventType::TYPE_WEEKLY)) {
                switch ($command->type) {
                    case EventType::TYPE_SINGLE:
                        $command->startDate = $occurrence->startDate();
                        $command->endDate = $occurrence->endDate();

                        /** @var Event $newEvent */
                        $newEvent = $this->eventFactory->createFromCommand($command);
                        $occurrence->moveToEvent($newEvent);
                        $occurrences = new ArrayCollection([$occurrence]);
                        $newEvent->setOccurrences($occurrences);
                        $this->occurrenceRepository->update($occurrences);
                        $this->eventRepository->insert($newEvent);
                        break;

                    case EventType::TYPE_WEEKLY:
                        $occurrence->changeStartDate($command->startDate);
                        $occurrence->changeDuration(new OccurrenceDuration($command->duration));
                        $this->occurrenceRepository->update($occurrence);
                        break;
                }
            }

            $this->eventRepository->update($event);
        }
    }
}
