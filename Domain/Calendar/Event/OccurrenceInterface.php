<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\IdInterface;

/**
 * Interface OccurrenceInterface.
 *
 * @property DateTime $startDate
 * @property DateTime $endDate
 * @property OccurrenceDuration $duration
 * @property bool $modified
 * @property Event $event
 * @property string $occurrenceId
 */
interface OccurrenceInterface
{
    public function resize(OccurrenceDuration $newDuration);

    public function move(DateTime $newStartDate);

    public function isOngoing() : bool;

    public function isPast() : bool;

    public function startDate() : DateTime;

    public function duration() : OccurrenceDuration;

    public function endDate() : DateTime;

    public function id() : IdInterface;

    public function event() : Event;

    public function changeStartDate(DateTime $startDate);

    public function changeDuration(OccurrenceDuration $duration);

    public function isModified() : bool;

    public function isDeleted() : bool;

    public function synchronizeWithEvent();
}
