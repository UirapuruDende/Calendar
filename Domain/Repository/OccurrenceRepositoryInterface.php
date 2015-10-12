<?php
namespace Dende\Calendar\Domain\Repository;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface OccurrenceRepositoryInterface
 * @package Gyman\Domain\Repository
 */
interface OccurrenceRepositoryInterface
{
    /**
     * @param $occurrence
     * @return mixed
     */
    public function insert(Occurrence $occurrence);

    /**
     * @param Event $event
     * @return ArrayCollection|Occurrence[]
     */
    public function findAllByEvent(Event $event);

    /**
     * @param DateTime $date
     * @param Calendar $calendar
     * @return mixed
     */
    public function findOneByDateAndCalendar(DateTime $date, Calendar $calendar);

    /**
     * @return ArrayCollection|array|Occurrence[]
     */
    public function findAll();

    /**
     * @param $occurrence
     * @return mixed
     */
    public function update(Occurrence $occurrence);

    /**
     * @param Event $event
     * @return Occurrence[]
     */
    public function findAllByEventUnmodified(Event $event);
}
