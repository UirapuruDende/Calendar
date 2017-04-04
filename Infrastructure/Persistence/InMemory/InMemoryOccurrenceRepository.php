<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory;

use ArrayAccess;
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
final class InMemoryOccurrenceRepository implements OccurrenceRepositoryInterface, ArrayAccess
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

    public function mergeCollection(ArrayCollection $occurrences)
    {
        foreach($occurrences as $occurrence) {
            $this->occurrences[$occurrence->id()->__toString()] = $occurrence;
        }
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

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) : bool
    {
        return $this->occurrences->offsetExists($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->occurrences->offsetGet($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->occurrences->offsetSet($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->occurrences->offsetUnset($offset);
    }
}
