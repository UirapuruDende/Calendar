<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Handler\UpdateStrategy\UpdateStrategyInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Repository\CalendarRepositoryInterface;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Exception;

/**
 * Class RemoveEventHandler
 * @package Dende\Calendar\Application\Handler
 */
final class RemoveCalendarHandler
{
    /**
     * @var RemoveEventHandler
     */
    private $eventRemoveHandler;

    /**
     * @var CalendarRepositoryInterface
     */
    private $calendarRepository;


    /**
     * @param UpdateEventCommand $command
     */
    public function remove(Calendar $calendar)
    {
        $this->eventRemoveHandler->removeCollection($calendar->events());
        $this->calendarRepository->remove($calendar);
    }
}
