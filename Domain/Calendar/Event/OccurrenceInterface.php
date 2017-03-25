<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;

/**
 * Interface OccurrenceInterface.
 *
 * @property DateTime $startDate
 * @property DateTime $endDate
 * @property OccurrenceDuration $duration
 * @property bool $modified
 * @property Event $event
 * @property string $id
 */
interface OccurrenceInterface
{
    /**
     * @param OccurrenceDuration $newDuration
     */
    public function resize(OccurrenceDuration $newDuration);

    /**
     * @param DateTime $newStartDate
     */
    public function move(DateTime $newStartDate);

    /**
     * @return bool
     */
    public function isOngoing();

    /**
     * @return bool
     */
    public function isPast();

    /**
     * @return DateTime
     */
    public function startDate();

    /**
     * @return OccurrenceDuration
     */
    public function duration();

    /**
     * @return Event
     */
    public function event();

    /**
     * @deprecated
     */
    public function resetToEvent();

    /**
     * @return DateTime
     */
    public function endDate();

    /**
     * @return string
     */
    public function id();

    /**
     * @param DateTime $startDate
     */
    public function changeStartDate(DateTime $startDate);

    /**
     * @param OccurrenceDuration $duration
     */
    public function changeDuration(OccurrenceDuration $duration);

    public function isModified();

    public function synchronizeWithEvent();

    public function moveToEvent(Event $event);
}
