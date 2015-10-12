<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory\Specification;

use Carbon\Carbon;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\Specification\InMemoryOccurrenceSpecificationInterface;

/**
 * Class InMemoryOccurrenceByWeekSpecification
 * @package Dende\Calendar\Infrastructure\Persistence\InMemory\Specification
 */
final class InMemoryOccurrenceByWeekSpecification implements InMemoryOccurrenceSpecificationInterface
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
     * @param \DateTime $rangeStart
     * @param \DateTime $rangeEnd
     */
    public function __construct($year, $weekNumber)
    {
        $weekStart = Carbon::instance(new \DateTime(sprintf('%d-W%d-1', $year, $weekNumber)));
        $weekEnd = clone($weekStart);
        $weekEnd->addDays(7);

        $this->rangeStart = $weekStart;
        $this->rangeEnd = $weekEnd;
    }

    /**
     * @param Occurrence $occurrence
     * @return bool
     * @internal param Event $event
     */
    public function specifies(Occurrence $occurrence)
    {
        $date = $occurrence->startDate();

        if ($date >= $this->rangeStart && $date < $this->rangeEnd) {
            return true;
        }

        return false;
    }
}
