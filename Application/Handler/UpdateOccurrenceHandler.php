<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Application\Command\UpdateOccurrenceCommand;
use Dende\Calendar\Application\Handler\UpdateStrategy\UpdateStrategyInterface;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Exception;

/**
 * Class CreateEventHandler.
 */
final class UpdateOccurrenceHandler
{
    /**
     * @var OccurrenceRepositoryInterface
     */
    private $occurrenceRepository;

    /**
     * CreateEventHandler constructor.
     *
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     */
    public function __construct(
        OccurrenceRepositoryInterface $occurrenceRepository
    ) {
        $this->occurrenceRepository = $occurrenceRepository;
    }

    /**
     * @param UpdateEventCommandInterface $command
     *
     * @throws Exception
     */
    public function handle(UpdateOccurrenceCommand $command)
    {
        die(var_dump($command));
    }
}
