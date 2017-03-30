<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory\Specification;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\Specification\InMemoryEventSpecificationInterface;

/**
 * Class InMemoryOccurrenceByDateRateAndCalendarSpecification.
 */
final class InMemoryEventsByDateRangeAndCalendarSpecification implements InMemoryEventSpecificationInterface
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
        $end   = Carbon::instance($rangeEnd);

        $start->setTime(0, 0, 0);
        $end->setTime(23, 59, 59);

        $this->calendar = $calendar;

        $this->rangeStart = $start;
        $this->rangeEnd   = $end;
    }

    /**
     * @param Occurrence $event
     *
     * @return bool
     *
     * @internal param Event $event
     */
    public function specifies(Event $event)
    {
        $calendarId = $event->calendar()->id();

        if (!$calendarId === $this->calendar->id()) {
            return false;
        }

        $dates = $event->calculateOccurrencesDates();
        $that  = $this;

        $occurrencesDates = array_filter($dates->toArray(), function (DateTime $date) use ($that) {
            if ($date >= $this->rangeStart && $date < $this->rangeEnd) {
                return true;
            }

            return false;
        });

        return count($occurrencesDates) > 0;
    }
}
