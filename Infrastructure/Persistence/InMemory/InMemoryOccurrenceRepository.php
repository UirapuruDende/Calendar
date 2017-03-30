<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory;

use DateTime;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Class InMemoryOccurrenceRepository.
 */
final class InMemoryOccurrenceRepository implements OccurrenceRepositoryInterface
{
    /**
     * @var Occurrence[]|ArrayCollection
     */
    private $occurrences;

    /**
     * InMemoryOccurrenceRepository constructor.
     *
     * @param Occurrence[]|ArrayCollection $occurrences
     */
    public function __construct(ArrayCollection $occurrences = null)
    {
        $this->occurrences = $occurrences ?: new ArrayCollection();
    }

    /**
     * @return Occurrence[]
     */
    public function findAll() : ArrayCollection
    {
        return $this->occurrences;
    }

    /**
     * @param Occurrence $occurrence
     */
    public function insert(Occurrence $occurrence)
    {
        $this->occurrences[$occurrence->id()->__toString()] = $occurrence;
    }

    /**
     * @param $event
     *
     * @return ArrayCollection|Occurrence[]
     */
    public function findAllByEvent(Event $event) : ArrayCollection
    {
        $criteria = Criteria::create();
        $expr     = Criteria::expr();

        $criteria->andWhere($expr->eq('event', $event));

        return $this->occurrences->matching($criteria);
    }

    /**
     * @param DateTime $date
     * @param Calendar $calendar
     *
     * @return Occurrence[]|ArrayCollection
     */
    public function findByDateAndCalendar(DateTime $date, Calendar $calendar) : ArrayCollection
    {
        return $this->occurrences->filter(function (Occurrence $occurrence) use ($calendar, $date) {
            return $occurrence->event()->calendar() === $calendar
                && $occurrence->startDate() <= $date
                && $occurrence->endDate() >= $date;
        });
    }

    /**
     * @param Occurrence $occurrence
     */
    public function update(Occurrence $occurrence)
    {
        $this->occurrences[$occurrence->id()->__toString()] = $occurrence;
    }

    /**
     * @return mixed
     */
    public function findAllByEventUnmodified(Event $event) : ArrayCollection
    {
        $occurrences = array_filter($this->occurrences->getIterator(), function (Occurrence $occurrence) use ($event) {
            return $occurrence->event()->id()->equals($event->id());
        });

        return new ArrayCollection($occurrences);
    }

    /**
     * @param Occurrence|Event\Occurrence[]|ArrayCollection $occurrence
     */
    public function remove(Occurrence $occurrence)
    {
        unset($this->occurrences[$occurrence->id()->__toString()]);
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

    /**
     * @param string $id
     *
     * @return Occurrence|null
     */
    public function findOneById(string $id)
    {
        return $this->occurrences->get($id);
    }
}
