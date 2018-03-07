<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\CreateCalendarCommand;
use Dende\Calendar\Application\Event\PostCreateCalendar;
use Dende\Calendar\Application\Events;
use Dende\Calendar\Application\Factory\CalendarFactoryInterface;
use Dende\Calendar\Application\Repository\CalendarRepositoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateCalendarHandler
{

    /**
     * @var CalendarRepositoryInterface
     */
    private $calendarRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(CalendarRepositoryInterface $calendarRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->calendarRepository = $calendarRepository;
        $this->eventDispatcher    = $eventDispatcher;
    }

    public function handle(CreateCalendarCommand $command)
    {
        $calendar = Calendar::create($command->title());

        $this->calendarRepository->insert($calendar);

        $this->eventDispatcher->dispatch(
            Events::POST_CREATE_CALENDAR,
            new PostCreateCalendar($calendar)
        );
    }
}
