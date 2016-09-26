<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory\Specification;

use Carbon\Carbon;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\Specification\InMemoryOccurrenceSpecificationInterface;

/**
 * Class InMemoryOccurrenceByDateRateAndCalendarSpecification.
 */
final class InMemoryOccurrenceByDateRangeAndCalendarSpecification implements InMemoryOccurrenceSpecificationInterface
{
    /**
     * @var \DateTime
     */
    private $rangeStart;

    /**
     * @var \DateTime
     */
    private $rangeEnd;

    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * InMemoryEventByWeekSpecificationInterface constructor.
     *
     * @param \DateTime $rangeStart
     * @param \DateTime $rangeEnd
     */
    public function __construct(\DateTime $rangeStart, \DateTime $rangeEnd, Calendar $calendar)
    {
        $start = Carbon::instance($rangeStart);
        $end = Carbon::instance($rangeEnd);

        $start->setTime(0, 0, 0);
        $end->setTime(23, 59, 59);

        $this->calendar = $calendar;

        $this->rangeStart = $start;
        $this->rangeEnd = $end;
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
        $date = $occurrence->startDate();

        $calendarId = $occurrence->event()->calendar()->id();

        if (!$calendarId === $this->calendar->id()) {
            return false;
        }

        if ($date >= $this->rangeStart && $date < $this->rangeEnd) {
            return true;
        }

        return false;
    }
}
