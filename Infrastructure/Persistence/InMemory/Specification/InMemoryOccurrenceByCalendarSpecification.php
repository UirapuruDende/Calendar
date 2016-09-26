<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory\Specification;

use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\Specification\InMemoryOccurrenceSpecificationInterface;

/**
 * Class InMemoryOccurrenceByCalendarSpecification.
 */
final class InMemoryOccurrenceByCalendarSpecification implements InMemoryOccurrenceSpecificationInterface
{
    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * InMemoryEventByWeekSpecificationInterface constructor.
     */
    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @param Occurrence $occurrence
     *
     * @return bool
     *
     * @internal param Event $event
     */
    public function specifies(Occurrence $occurrence)
    {
        $calendarId = $occurrence->event()->calendar()->id();

        if ($calendarId === $this->calendar->id()) {
            return true;
        }

        return false;
    }
}
