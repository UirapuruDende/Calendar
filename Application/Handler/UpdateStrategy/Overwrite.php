<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

/**
 * Class AllInclusive
 * @package Dende\Calendar\Application\Handler\UpdateStrategy
 * @property OccurrenceRepositoryInterface occurrenceRepository
 * @property EventRepositoryInterface eventRepository
 */
final class Overwrite implements UpdateStrategyInterface
{
    use SetRepositoriesTrait;

    /**
     * @param UpdateEventCommand $command
     * @return null
     */
    public function update(UpdateEventCommand $command)
    {
        $event = $command->occurrence->event();

        $event->changeDuration(new Duration($command->duration));
        $event->changeStartDate($command->startDate);
        $event->changeEndDate($command->endDate);
        $event->changeTitle($command->title);
        $event->changeRepetitions(new Repetitions($command->repetitionDays));

        foreach($event->occurrences() as $occurrence) {
            $this->occurrenceRepository->remove($occurrence);
        }

        $occurrences = OccurrenceFactory::generateCollectionFromEvent($event);

        $event->setOccurrences($occurrences);

        $this->eventRepository->update($event);

        foreach($event->occurrences() as $occurrence) {
            $this->occurrenceRepository->insert($occurrence);
        }
    }
}
