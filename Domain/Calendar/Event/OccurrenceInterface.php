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
    public function isOngoing() : bool;

    /**
     * @return bool
     */
    public function isPast() : bool;

    /**
     * @return DateTime
     */
    public function startDate() : DateTime;

    /**
     * @return OccurrenceDuration
     */
    public function duration() : OccurrenceDuration;

    /**
     * @return DateTime
     */
    public function endDate() : DateTime;

    /**
     * @return string
     */
    public function id() : string;

    /**
     * @param DateTime $startDate
     */
    public function changeStartDate(DateTime $startDate);

    /**
     * @param OccurrenceDuration $duration
     */
    public function changeDuration(OccurrenceDuration $duration);

    public function isModified() : bool;

    public function synchronizeWithEvent(Event $event);
}
