<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory\Specification;

use Carbon\Carbon;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\Specification\InMemoryOccurrenceSpecificationInterface;

final class InMemoryOccurrenceByDateAndCalendarSpecification implements InMemoryOccurrenceSpecificationInterface
{
    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * @var Carbon
     */
    private $date;

    /**
     * InMemoryEventByWeekSpecificationInterface constructor.
     *
     * @param \DateTime $rangeStart
     * @param \DateTime $rangeEnd
     */
    public function __construct(\DateTime $date, Calendar $calendar)
    {
        $this->date = Carbon::instance($date);

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
        $rangeStart = $occurrence->startDate();
        $rangeEnd = $occurrence->endDate();

        $calendarId = $occurrence->event()->calendar()->id();

        if (!$calendarId === $this->calendar->id()) {
            return false;
        }

        if ($rangeStart <= $this->date && $this->date < $rangeEnd) {
            return true;
        }

        return false;
    }
}
