<?php
namespace Dende\Calendar\Application\Service;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

/**
 * Class FindCurrentEvent
 * @package Dende\Calendar\Application\Service
 */
final class FindCurrentEvent
{
    /**
     * @var OccurrenceRepositoryInterface
     */
    private $occurrenceRepository;

    /**
     * FindCurrentEvent constructor.
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     */
    public function __construct(OccurrenceRepositoryInterface $occurrenceRepository)
    {
        $this->occurrenceRepository = $occurrenceRepository;
    }

    /**
     * @param Calendar $calendar
     * @return Event
     */
    public function getCurrentEvent(Calendar $calendar)
    {
        $result = $this->occurrenceRepository->findOneByDateAndCalendar(new DateTime('now'), $calendar);

        if (count($result) === 0) {
            return;
        }

        /** @var Occurrence $currentOccurrence */
        $currentOccurrence = $result->first();

        $event = $currentOccurrence->event();

        return $event;
    }
}
