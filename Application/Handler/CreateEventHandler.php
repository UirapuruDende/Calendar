<?php
namespace Dende\Calendar\Application\Handler;

use Carbon\Carbon;
use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Repository\CalendarRepositoryInterface;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Exception;

/**
 * Class CreateEventHandler.
 */
final class CreateEventHandler
{
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var OccurrenceRepositoryInterface
     */
    private $occurrenceRepository;

    /**
     * @var CalendarRepositoryInterface
     */
    private $calendarRepository;

    /**
     * CreateEventHandler constructor.
     *
     * @param CalendarRepositoryInterface   $calendarRepository
     * @param EventRepositoryInterface      $eventRepository
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     */
    public function __construct(CalendarRepositoryInterface $calendarRepository, EventRepositoryInterface $eventRepository, OccurrenceRepositoryInterface $occurrenceRepository)
    {
        $this->calendarRepository   = $calendarRepository;
        $this->eventRepository      = $eventRepository;
        $this->occurrenceRepository = $occurrenceRepository;
    }

    /**
     * @param CreateEventCommand $command
     *
     * @throws Exception
     */
    public function handle(CreateEventCommand $command)
    {
        if ($command->type === EventType::TYPE_SINGLE) {
            $endDate = Carbon::instance($command->startDate)
                ->addMinutes(Duration::calculate($command->startDate, $command->endDate)->minutes());

            $repetitions = [];
        } else {
            $endDate = Carbon::instance($command->endDate);

            if (0 === count($command->repetitions)) {
                throw new Exception('If event is repetive, you should choose days of repetition for it!');
            }

            $repetitions = $command->repetitions;
        }

        /** @var Calendar $calendar */
        $calendar = $this->calendarRepository->findOneByCalendarId($command->calendarId);

        if (null === $calendar) {
            throw new Exception('Calendar is null and it has to be set!');
        }

        $eventId = EventId::create();

        $calendar->addEvent(
            $eventId,
            $command->title,
            $command->startDate,
            $endDate,
            new EventType($command->type),
            new Repetitions($repetitions)
        );

        $event = $calendar->getEventById($eventId);

        $this->eventRepository->insert($event);

        foreach ($event->occurrences() as $occurrence) {
            $this->occurrenceRepository->insert($occurrence);
        }
    }
}
