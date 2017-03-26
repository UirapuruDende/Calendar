<?php
namespace Dende\Calendar\Application\Handler;

use Carbon\Carbon;
use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
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
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @var OccurrenceFactory
     */
    private $occurrenceFactory;

    /**
     * CreateEventHandler constructor.
     *
     * @param EventRepositoryInterface      $eventRepository
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     * @param EventFactory                  $eventFactory
     * @param OccurrenceFactory             $occurrenceFactory
     */
    public function __construct(EventRepositoryInterface $eventRepository, OccurrenceRepositoryInterface $occurrenceRepository, EventFactoryInterface $eventFactory, OccurrenceFactoryInterface $occurrenceFactory)
    {
        $this->eventRepository = $eventRepository;
        $this->occurrenceRepository = $occurrenceRepository;
        $this->eventFactory = $eventFactory;
        $this->occurrenceFactory = $occurrenceFactory;
    }

    /**
     * @param CreateEventCommand $command
     */
    public function handle(CreateEventCommand $command)
    {
        if (is_null($command->calendar)) {
            throw new Exception('Calendar is null and it has to be set!');
        }

        if ($command->type === EventType::TYPE_SINGLE) {
            /** @var Carbon $date */
            $date = Carbon::instance($command->startDate)
                ->addMinutes(Duration::calculate($command->startDate, $command->endDate)->minutes());
        } else {
            /** @var Carbon $date */
            $date = Carbon::instance($command->endDate);
        }

        $command->endDate = $date;

        $event = $this->eventFactory->createFromCommand($command);
        $occurrences = $this->occurrenceFactory->generateCollectionFromEvent($event);

        $event->setOccurrences($occurrences);
        $this->occurrenceRepository->insert($occurrences);
//        $this->eventRepository->insert($event);
    }
}
