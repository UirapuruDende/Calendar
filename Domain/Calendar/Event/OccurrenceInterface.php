<?php
namespace Dende\Calendar\Domain\Calendar\Event;
use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;

/**
 * Interface OccurrenceInterface
 * @package Dende\Calendar\Domain\Calendar\Event
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
    function resize(OccurrenceDuration $newDuration);

    /**
     * @param DateTime $newStartDate
     */
    function move(DateTime $newStartDate);

    /**
     * @return bool
     */
    function isOngoing();

    /**
     * @return bool
     */
    function isPast();

    /**
     * @return DateTime
     */
    function startDate();

    /**
     * @return OccurrenceDuration
     */
    function duration();

    /**
     * @return Event
     */
    function event();

    /**
     * @deprecated
     */
    function resetToEvent();

    /**
     * @return DateTime
     */
    function endDate();

    /**
     * @return string
     */
    function id();

    /**
     * @param DateTime $startDate
     */
    function changeStartDate(DateTime $startDate);

    /**
     * @param OccurrenceDuration $duration
     */
    function changeDuration(OccurrenceDuration $duration);

    function isModified();

    function synchronizeWithEvent();

    function moveToEvent(Event $event);
}