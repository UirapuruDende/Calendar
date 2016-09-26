<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Repository\CalendarRepositoryInterface;

/**
 * Class RemoveEventHandler.
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
