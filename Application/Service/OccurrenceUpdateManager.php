<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\UpdateCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Application\Event\PostUpdateEvent;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Dende\Calendar\Application\Service\UpdateStrategy\UpdateStrategyInterface;
use Exception;

/**
 * Class CreateEventHandler.
 */
final class OccurrenceUpdateManager
{
    const MODE_SINGLE         = 'single';
    const MODE_ALL_INCLUSIVE  = 'allinclusive';
    const MODE_ALL_EXCLUSIVE  = 'allexclusive';
    const MODE_NEXT_INCLUSIVE = 'nextinclusive';
    const MODE_NEXT_EXCLUSIVE = 'nextexclusive';
    const MODE_OVERWRITE      = 'overwrite';

    /**
     * @var array
     */
    public static $availableModes = [
        self::MODE_SINGLE,
//        self::MODE_ALL_INCLUSIVE,
//        self::MODE_ALL_EXCLUSIVE,
        self::MODE_NEXT_INCLUSIVE,
//        self::MODE_NEXT_EXCLUSIVE,
        self::MODE_OVERWRITE,
    ];

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var OccurrenceRepositoryInterface
     */
    private $occurrenceRepository;

    /**
     * @var UpdateStrategyInterface[]
     */
    private $strategy = [];

    /**
     * CreateEventHandler constructor.
     *
     * @param EventRepositoryInterface      $eventRepository
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     */
    public function __construct(
        EventRepositoryInterface $eventRepository,
        OccurrenceRepositoryInterface $occurrenceRepository
    ) {
        $this->eventRepository      = $eventRepository;
        $this->occurrenceRepository = $occurrenceRepository;
    }

    public function addStrategy(string $name, UpdateStrategyInterface $strategy)
    {
        $strategy->setEventRepository($this->eventRepository);
        $strategy->setOccurrenceRepository($this->occurrenceRepository);

        $this->strategy[$name] = $strategy;
    }

    /**
     * @param UpdateEventCommandInterface $command
     *
     * @throws Exception
     */
    public function handle(UpdateCommand $command)
    {
        if (!array_key_exists($command->method(), $this->strategy)) {
            throw new Exception(sprintf(
                "Mode '%s' not allowed. Only %s allowed.",
                $command->method(),
                implode(', ', array_keys($this->strategy))
            ));
        }

        $this->strategy[$command->method()]->update($command);
    }

    public function postEventUpdate(PostUpdateEvent $updateEvent)
    {
        $event = $updateEvent->getEvent();

        $this->handle(new UpdateCommand(
            $updateEvent->getOccurrenceId(),
            $updateEvent->getMethod(),
            $event->startDate(),
            $event->endDate(),
            $event->repetitions()
        ));
    }
}
