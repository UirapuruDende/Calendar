<?php
namespace Dende\Infrastructure\Persistence\Doctrine;

use Dende\Calendar\Domain\Repository\EventRepositoryInterface;

class DoctrineEventRepository implements EventRepositoryInterface
{
    /**
     * @param $event
     * @return mixed
     */
    public function insert($event)
    {
        // TODO: Implement insert() method.
    }

    public function findAllByCalendar($calendar)
    {
        // TODO: Implement findAllByCalendar() method.
    }

    public function findAllByDay($date)
    {
        // TODO: Implement findAllByDay() method.
    }

    public function findAllByWeek($date)
    {
        // TODO: Implement findAllByWeek() method.
    }

    public function findAllByMonth($date)
    {
        // TODO: Implement findAllByMonth() method.
    }
}
