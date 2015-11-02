<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

/**
 * Class RemoveOccurrenceHandler
 * @package Dende\Calendar\Application\Handler
 */
final class RemoveOccurrenceHandler
{
    /**
     * @var OccurrenceRepositoryInterface
     */
    private $occurrenceRepository;

    /**
     * CreateEventHandler constructor.
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     * @internal param EventRepositoryInterface $eventRepository
     */
    public function __construct(OccurrenceRepositoryInterface $occurrenceRepository)
    {
        $this->occurrenceRepository = $occurrenceRepository;
    }

    /**
     * @param Occurrence $occurrence
     */
    public function remove(Occurrence $occurrence)
    {
        $this->occurrenceRepository->remove($occurrence);
    }
}
