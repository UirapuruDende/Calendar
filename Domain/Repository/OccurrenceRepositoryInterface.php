<?php
namespace Dende\Calendar\Domain\Repository;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface OccurrenceRepositoryInterface.
 */
interface OccurrenceRepositoryInterface
{
    /**
     * @param $occurrence
     *
     * @return mixed
     */
    public function insert($occurrence);

    /**
     * @param Event $event
     *
     * @return ArrayCollection|Occurrence[]
     */
    public function findAllByEvent(Event $event);

    /**
     * @param DateTime $date
     * @param Calendar $calendar
     *
     * @return mixed
     */
    public function findOneByDateAndCalendar(DateTime $date, Calendar $calendar);

    /**
     * @return ArrayCollection|array|Occurrence[]
     */
    public function findAll();

    /**
     * @param Occurrence|Occurrence[] $occurrence
     */
    public function update($occurrence);

    /**
     * @param Event $event
     *
     * @return Occurrence[]
     */
    public function findAllByEventUnmodified(Event $event);

    /**
     * @param Occurrence|Occurrence[]|ArrayCollection $occurrences
     *
     * @throws \Exception
     */
    public function remove($occurrences);

    /**
     * @param Event $event
     */
    public function removeAllForEvent(Event $event);
}
