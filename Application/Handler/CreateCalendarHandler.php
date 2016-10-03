<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Command\EventCommandInterface;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\CalendarFactory;
use Dende\Calendar\Application\Factory\CalendarFactoryInterface;
use Dende\Calendar\Domain\Repository\CalendarRepositoryInterface;
use Dende\CalendarBundle\Event\CalendarAfterCreationEvent;
use Dende\CalendarBundle\Events;
use Dende\CalendarBundle\Repository\ORM\CalendarRepository;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class NewCalendarCreationHandler.
 */
class CreateCalendarHandler
{
    /**
     * @var CalendarFactoryInterface
     */
    private $calendarFactory;

    /**
     * @var CalendarRepositoryInterface
     */
    private $calendarRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * NewCalendarCreationHandler constructor.
     *
     * @param CalendarFactory    $calendarFactory
     * @param CalendarRepository $calendarRepository
     */
    public function __construct(CalendarFactoryInterface $calendarFactory, CalendarRepositoryInterface $calendarRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->calendarFactory = $calendarFactory;
        $this->calendarRepository = $calendarRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param UpdateEventCommand|CreateEventCommand $command
     */
    public function handleForm(EventCommandInterface $command)
    {
        $newCalendarName = $command->newCalendarName;

        if (is_null($newCalendarName)) {
            throw new Exception('Calendar name is required!');
        }

        $newCalendar = $this->calendarFactory->createFromArray(['title' => $newCalendarName]);
        $this->calendarRepository->insert($newCalendar);

        $this->eventDispatcher->dispatch(
            Events::CALENDAR_AFTER_CREATION,
            new CalendarAfterCreationEvent($newCalendar)
        );

        $command->calendar = $newCalendar;
    }
}
