<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\UpdateStrategy\UpdateStrategyInterface;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Exception;

/**
 * Class CreateEventHandler.
 */
final class UpdateEventHandler
{
    /**
     * @todo: remove const and hardcoded strategies, let them register themselve and use existing ones
     */
    const MODE_SINGLE = 'single';
    const MODE_ALL_INCLUSIVE = 'all_inclusive';
    const MODE_ALL_EXCLUSIVE = 'all_exclusive';
    const MODE_NEXT_INCLUSIVE = 'nextinclusive';
    const MODE_NEXT_EXCLUSIVE = 'next_exclusive';
    const MODE_OVERWRITE = 'overwrite';

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
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @var OccurrenceFactoryInterface
     */
    private $occurrenceFactory;

    /**
     * CreateEventHandler constructor.
     *
     * @param EventRepositoryInterface $eventRepository
     */
    public function __construct(
        EventRepositoryInterface $eventRepository,
        OccurrenceRepositoryInterface $occurrenceRepository,
        EventFactoryInterface $eventFactory,
        OccurrenceFactoryInterface $occurrenceFactory
    ) {
        $this->eventRepository = $eventRepository;
        $this->occurrenceRepository = $occurrenceRepository;
        $this->eventFactory = $eventFactory;
        $this->occurrenceFactory = $occurrenceFactory;
    }

    public function addStrategy($name, UpdateStrategyInterface $strategy)
    {
        if (!in_array($name, self::$availableModes)) {
            throw new Exception(sprintf(
                "Strategy '%s' not allowed. Only %s allowed.",
                $name,
                implode(', ', self::$availableModes)
            ));
        }

        if (array_key_exists($name, $this->strategy)) {
            throw new Exception(sprintf("Strategy name '%s' already set!", $name));
        }

        $strategy->setEventRepository($this->eventRepository);
        $strategy->setOccurrenceRepository($this->occurrenceRepository);
        $strategy->setEventFactory($this->eventFactory);
        $strategy->setOccurrenceFactory($this->occurrenceFactory);

        $this->strategy[$name] = $strategy;
    }

    /**
     * @param UpdateEventCommand|RemoveEventCommand $command
     */
    public function handle(UpdateEventCommandInterface $command)
    {
        if (!in_array($command->method, self::$availableModes)) {
            throw new Exception(sprintf(
                "Mode '%s' not allowed. Only %s allowed.",
                $command->method,
                implode(', ', self::$availableModes)
            ));
        }

        if (!array_key_exists($command->method, $this->strategy)) {
            throw new Exception(sprintf(
                "Strategy '%s' has not been added. Use UpdateEventHandler::addStrategy() method to add it",
                $command->method
            ));
        }

        $this->strategy[$command->method]->update($command);
    }
}
