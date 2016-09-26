<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Dende\Calendar\Domain\Repository\Specification\InMemoryOccurrenceSpecificationInterface;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryOccurrenceByCalendarSpecification;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryOccurrenceByDateAndCalendarSpecification;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryOccurrenceByDateRangeAndCalendarSpecification;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryOccurrenceByEventSpecification;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery\CountValidator\Exception;

/**
 * Class InMemoryOccurrenceRepository.
 */
final class InMemoryOccurrenceRepository implements OccurrenceRepositoryInterface
{
    /**
     * @var Occurrence[]
     */
    private $occurrences = [];

    /**
     * @return Occurrence[]
     */
    public function findAll()
    {
        return $this->occurrences;
    }

    /**
     * @param Occurrence $occurrence
     */
    public function insert($occurrence)
    {
        $this->occurrences[$occurrence->id()] = $occurrence;
    }

    /**
     * @param Occurrence[] $occurrences
     */
    public function insertCollection($occurrences)
    {
        foreach ($occurrences as $occurrence) {
            $this->occurrences[$occurrence->id()] = $occurrence;
        }
    }

    /**
     * @param $event
     *
     * @return ArrayCollection|Occurrence[]
     */
    public function findAllByEvent(Event $event)
    {
        return $this->query(
            new InMemoryOccurrenceByEventSpecification($event)
        );
    }

    /**
     * @param InMemoryOccurrenceSpecificationInterface $specification
     *
     * @return ArrayCollection|Occurrence[]
     */
    public function query(InMemoryOccurrenceSpecificationInterface $specification)
    {
        $result = $this->filterOccurrences(
            function (Occurrence $occurrence) use ($specification) {
                return $specification->specifies($occurrence);
            }
        );

        return new ArrayCollection($result);
    }

    private function filterOccurrences(callable $callback)
    {
        $result = array_values(array_filter($this->occurrences, $callback));

        usort($result, function (Occurrence $a, Occurrence $b) {
            return $a->startDate() < $b->startDate() ? -1 : 1;
        });

        return $result;
    }

    /**
     * @param DateTime $date
     * @param Calendar $calendar
     *
     * @return Occurrence[]|ArrayCollection
     */
    public function findOneByDateAndCalendar(DateTime $date, Calendar $calendar)
    {
        return $this->query(
            new InMemoryOccurrenceByDateAndCalendarSpecification($date, $calendar)
        );
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param Calendar $calendar
     *
     * @return Occurrence[]|ArrayCollection
     */
    public function findAllByCalendarInDateRange(DateTime $startDate, DateTime $endDate, Calendar $calendar)
    {
        return $this->query(
            new InMemoryOccurrenceByDateRangeAndCalendarSpecification($startDate, $endDate, $calendar)
        );
    }

    /**
     * @param $calendar
     *
     * @return Occurrence[]|ArrayCollection
     */
    public function findAllByCalendar($calendar)
    {
        return $this->query(
            new InMemoryOccurrenceByCalendarSpecification($calendar)
        );
    }

    /**
     * @param Occurrence $occurrence
     */
    public function update($occurrence)
    {
        if (!isset($this->occurrences[$occurrence->id()])) {
            throw new Exception(sprintf('Occurrence with id %s is not set, cannot update!', $occurrence->id()));
        }

        $this->occurrences[$occurrence->id()] = $occurrence;
    }

    /**
     * @return mixed
     */
    public function findAllByEventUnmodified(Event $event)
    {
        return $this->query(
            new InMemoryOccurrenceByEventSpecification($event, true)
        );
    }

    /**
     * @param Occurrence|Event\Occurrence[]|ArrayCollection $occurrence
     */
    public function remove($occurrence)
    {
        unset($this->occurrences[$occurrence->id()]);
    }

    /**
     * @param Event $event
     */
    public function removeAllForEvent(Event $event)
    {
        foreach ($this->occurrences as $occurrence) {
            if ($occurrence->event() === $event) {
                $this->remove($occurrence);
            }
        }
    }
}
