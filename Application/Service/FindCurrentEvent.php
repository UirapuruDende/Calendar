<?php
namespace Dende\Calendar\Application\Service;

use DateTime;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class FindCurrentEvent.
 */
final class FindCurrentEvent
{
    /**
     * @var OccurrenceRepositoryInterface
     */
    private $occurrenceRepository;

    /**
     * FindCurrentEvent constructor.
     *
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     */
    public function __construct(OccurrenceRepositoryInterface $occurrenceRepository)
    {
        $this->occurrenceRepository = $occurrenceRepository;
    }

    /**
     * @param Calendar $calendar
     *
     * @return ArrayCollection|Event[]
     */
    public function getCurrentEvents(Calendar $calendar) : ArrayCollection
    {
        $result = $this->occurrenceRepository->findByDateAndCalendar(new DateTime('now'), $calendar);

        return $result->map(function (Occurrence $occurrence) {
            return $occurrence->event();
        });
    }
}
