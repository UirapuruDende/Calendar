<?php
namespace Dende\Calendar\Application\Repository;

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
    public function insert(Occurrence $occurrence);

    public function findAllByEvent(Event $event) : ArrayCollection;

    public function findByDateAndCalendar(DateTime $date, Calendar $calendar) : ArrayCollection;

    public function findAll() : ArrayCollection;

    public function update(Occurrence $occurrence);

    public function findAllByEventUnmodified(Event $event);

    public function remove(Occurrence $occurrence);

    public function removeAllForEvent(Event $event);

    public function findOneById(string $id);
}
