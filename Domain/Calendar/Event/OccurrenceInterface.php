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
    public function resize(DurationInterface $duration);

    public function move(DateTime $startDate);

    public function isOngoing() : bool;

    public function isPast() : bool;

    public function startDate() : DateTime;

    public function duration() : DurationInterface;

    public function endDate() : DateTime;

    public function id() : IdInterface;

    public function event() : Event;

    public function isModified() : bool;

    public function synchronizeWithEvent();
}
