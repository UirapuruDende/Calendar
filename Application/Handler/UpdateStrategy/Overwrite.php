<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

/**
 * Class AllInclusive.
 *
 * @property OccurrenceRepositoryInterface|OccurrenceRepositoryInterface occurrenceRepository
 * @property EventRepositoryInterface eventRepository
 */
final class Overwrite implements UpdateStrategyInterface
{
    use SetRepositoriesTrait, SetFactoriesTrait;

    /**
     * @param UpdateEventCommandInterface|UpdateEventCommand|RemoveEventCommand $command
     */
    public function update(UpdateEventCommandInterface $command)
    {
        /** @var Event $event */
//        $event = $command->occurrence->event();

//        if ($command instanceof UpdateEventCommand) {

//            $event->updateWithCommand($command);

//            $event->setOccurrences($occurrences);
//            $this->eventRepository->update($event);
//            $this->occurrenceRepository->insert($occurrences);
//        } elseif ($command instanceof RemoveEventCommand) {
//            $this->occurrenceRepository->remove($event->occurrences());
//            $this->eventRepository->remove($event);
//        }
    }
}
