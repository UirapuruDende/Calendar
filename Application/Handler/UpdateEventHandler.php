<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventData;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;

class UpdateEventHandler
{
    /** @var  EventRepositoryInterface */
    private $eventRepository;

    /**
     * UpdateEventHandler constructor.
     * @param EventRepositoryInterface $eventRepository
     */
    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function handle(UpdateEventCommand $command)
    {
        /** @var Event $event */
        $event = $this->eventRepository->findOneById($command->eventId);
        $event->update(new EventData($command->startDate, $command->endDate, $command->title, new Repetitions($command->repetitions)));
        $this->eventRepository->update($event);
    }
}
