<?php
namespace Dende\Calendar\Application\Repository;

use Dende\Calendar\Domain\Calendar;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface CalendarRepositoryInterface.
 */
interface CalendarRepositoryInterface
{
    /**
     * @param Calendar $calendar
     */
    public function insert(Calendar $calendar);

    /**
     * @param Calendar $calendar
     */
    public function update(Calendar $calendar);

    /**
     * @return Calendar[]|ArrayCollection
     */
    public function findAll() : ArrayCollection;

    /**
     * @param Calendar $calendar
     */
    public function remove(Calendar $calendar);

    /**
     * @param string $calendarId
     *
     * @return Calendar|null
     */
    public function findOneByCalendarId(string $calendarId) : Calendar;
}
