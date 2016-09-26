<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory\Specification;

use Carbon\Carbon;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Repository\Specification\InMemoryEventSpecificationInterface;

/**
 * Class InMemoryEventByWeekSpecification.
 */
final class InMemoryEventByWeekSpecification implements InMemoryEventSpecificationInterface
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
     * InMemoryEventByWeekSpecificationInterface constructor.
     *
     * @param \DateTime $rangeStart
     * @param \DateTime $rangeEnd
     */
    public function __construct($year, $weekNumber)
    {
        $weekStart = Carbon::instance(new \DateTime(sprintf('%d-W%d-1', $year, $weekNumber)));
        $weekEnd = clone $weekStart;
        $weekEnd->addDays(7);

        $this->rangeStart = $weekStart;
        $this->rangeEnd = $weekEnd;
    }

    /**
     * @param Event $event
     *
     * @return bool
     */
    public function specifies(Event $event)
    {
        $dates = $event->calculateOccurrencesDates();

        foreach ($dates as $date) {
            if ($date >= $this->rangeStart && $date < $this->rangeEnd) {
                return true;
            }
        }

        return false;
    }
}
