<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\UpdateEventCommandInterface;
use Dende\Calendar\Application\Command\UpdateOccurrenceCommand;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceData;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;
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
        /** @var OccurrenceInterface $occurrence */
        $occurrence = $this->occurrenceRepository->findOneBy(["occurrenceId.id" => $command->occurrenceId()]);

        $occurrence->update(new OccurrenceData(
            $command->startDate(),
            OccurrenceDuration::calculate($command->startDate(), $command->endDate()))
        );

        $this->occurrenceRepository->update($occurrence);
    }
}
