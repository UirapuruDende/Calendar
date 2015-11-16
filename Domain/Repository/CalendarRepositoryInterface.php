<?php
namespace Dende\Calendar\Domain\Repository;
use Dende\Calendar\Domain\Calendar;

/**
 * Interface CalendarRepositoryInterface
 * @package Dende\Calendar\Domain\Repository
 */
interface CalendarRepositoryInterface
{
    /**
     * @param Calendar $calendar
     * @return void
     */
    public function insert(Calendar $calendar);

    /**
     * @param Calendar $calendar
     * @return void
     */
    public function update(Calendar $calendar);

    /**
     * @return Calendar[]
     */
    public function findAll();

    /**
     * @param Calendar $calendar
     * @return void
     */
    public function remove(Calendar $calendar);
}
