<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Event\PostUpdateEvent;
use Dende\Calendar\Application\Events;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventData;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateEventHandler
{
    /** @var  EventRepositoryInterface */
    private $eventRepository;

    /** @var  EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * UpdateEventHandler constructor.
     * @param EventRepositoryInterface $eventRepository
     */
    public function __construct(EventRepositoryInterface $eventRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->eventRepository = $eventRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(UpdateEventCommand $command)
    {
        /** @var Event $event */
        $event = $this->eventRepository->findOneById($command->eventId);
        $event->update(new EventData($command->startDate, $command->endDate, $command->title, new Repetitions($command->repetitions)));
        $this->eventRepository->update($event);

        $this->eventDispatcher->dispatch(Events::POST_UPDATE_EVENT, new PostUpdateEvent($event, $command->occurrenceId, $command->method));
    }
}
