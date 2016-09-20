<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

/**
 * Class RemoveOccurrenceHandler
 * @package Dende\Calendar\Application\Handler
 * @deprecated
 */
class RemoveOccurrenceHandler
{
    /**
     * @var RemoveEventHandler
     */
    private $removeEventHandler;

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * RemoveOccurrenceHandler constructor.
     * @param RemoveEventHandler $removeEventHandler
     * @param EventRepositoryInterface $eventRepository
     */
    public function __construct(RemoveEventHandler $removeEventHandler, EventRepositoryInterface $eventRepository)
    {
        $this->removeEventHandler = $removeEventHandler;
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param Occurrence $occurrence
     */
    public function remove(Occurrence $occurrence)
    {
        if(1 == count($occurrence->event()->occurrences())) {
            $this->removeEventHandler->remove($occurrence->event());
        } else {
            $event = $occurrence->event();
            $event->removeOccurrence($occurrence);
            $this->eventRepository->update($event);
        }
    }
}
