<?php
namespace Dende\Calendar\Domain\Repository;

use Dende\Calendar\Domain\Calendar;

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
     * @return Calendar[]
     */
    public function findAll();

    /**
     * @param Calendar $calendar
     */
    public function remove(Calendar $calendar);
}
