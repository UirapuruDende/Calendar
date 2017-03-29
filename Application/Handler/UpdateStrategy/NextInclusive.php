<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\CreateEventCommand;
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
     */
    public function update(UpdateEventCommandInterface $command)
    {
        /** @var Event $originalEvent */
        $originalEvent = $this->eventRepository->findOneByOccurrence($command->occurrence);

        if ($originalEvent->isSingle()) {
            throw new \Exception('This strategy is for series types events!');
        } elseif ($originalEvent->isWeekly()) {
            $pivotDate = $this->findPivotDate($command->occurrence, $originalEvent);
            $originalEvent->closeAtDate($pivotDate);

            $this->occurrenceRepository->update($originalEvent->occurrences());

            if ($command instanceof UpdateEventCommand) {
                $newEventCommand = CreateEventCommand::fromArray([
                    'startDate'      => $pivotDate,
                    'endDate'        => $command->endDate,
                    'calendar'       => $originalEvent->calendar(),
                    'type'           => $originalEvent->type()->type(),
                    'title'          => $command->title,
                    'repetitionDays' => $command->repetitionDays,
                ]);

                /** @var Event $newEvent */
                $newEvent = $this->eventFactory->createFromCommand($newEventCommand);
                $newEvent->generateOccurrencesCollection($this->occurrenceFactory);

                $this->eventRepository->insert($newEvent);
            }
        }

        $this->eventRepository->update($originalEvent);
    }

    /**
     * @param UpdateEventCommand $command
     *
     * @return DateTime
     */
    public function findPivotDate(Occurrence $clicked, Event $event) : DateTime
    {
        /** @var ArrayCollection|Occurrence[] $occurrences */
        $occurrences = $event->occurrences();

        /** @var ArrayCollection $beforeClicked */
        $beforeClicked = $occurrences->filter(function (Occurrence $occurrence) use ($clicked) {
            return $occurrence->endDate() <= $clicked->startDate();
        });

        $iterator = $beforeClicked->getIterator();

        $iterator->uasort(function (Occurrence $a, Occurrence $b) {
            return $a->startDate() > $b->startDate();
        });

        if ($latestOccurrence = end($iterator)) {
            return $latestOccurrence->endDate();
        } else {
            return $clicked->endDate();
        }
    }
}
