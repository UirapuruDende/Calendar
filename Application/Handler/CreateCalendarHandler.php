<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\CreateCalendarCommand;
use Dende\Calendar\Application\Event\PostCreateCalendar;
use Dende\Calendar\Application\Events;
use Dende\Calendar\Application\Factory\CalendarFactoryInterface;
use Dende\Calendar\Application\Repository\CalendarRepositoryInterface;
use Dende\Calendar\Domain\Calendar\CalendarId;
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
     * @param CalendarFactoryInterface    $calendarFactory
     * @param CalendarRepositoryInterface $calendarRepository
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(CalendarFactoryInterface $calendarFactory, CalendarRepositoryInterface $calendarRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->calendarFactory    = $calendarFactory;
        $this->calendarRepository = $calendarRepository;
        $this->eventDispatcher    = $eventDispatcher;
    }

    public function handle(CreateCalendarCommand $command)
    {
        if (is_null($command->title)) {
            throw new Exception('Calendar name is required!');
        }

        $calendar = $this->calendarFactory->createFromArray([
            'calendarId' => $command->calendarId ?: CalendarId::create(),
            'title'      => $command->title,
        ]);

        $this->calendarRepository->insert($calendar);

        $this->eventDispatcher->dispatch(
            Events::POST_CREATE_CALENDAR,
            new PostCreateCalendar($calendar)
        );
    }
}
