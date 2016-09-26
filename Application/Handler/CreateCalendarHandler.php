<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Factory\CalendarFactory;
use Dende\Calendar\Application\Factory\CalendarFactoryInterface;
use Dende\Calendar\Domain\Repository\CalendarRepositoryInterface;
use Dende\CalendarBundle\Event\CalendarAfterCreationEvent;
use Dende\CalendarBundle\Events;
use Dende\CalendarBundle\Repository\ORM\CalendarRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;

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
     * @param Form $form
     * @param $command
     */
    public function handleForm(Form $form, $command)
    {
        $newCalendarName = $form->get('new_calendar_name')->getData();

        if (!is_null($newCalendarName)) {
            $newCalendar = $this->calendarFactory->createFromArray(['title' => $newCalendarName]);
            $this->calendarRepository->insert($newCalendar);
            $this->eventDispatcher->dispatch(
                Events::CALENDAR_AFTER_CREATION,
                new CalendarAfterCreationEvent($newCalendar)
            );
            $command->calendar = $newCalendar;
        }
    }
}
