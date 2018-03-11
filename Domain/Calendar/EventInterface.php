<?php
namespace Dende\Calendar\Domain\Calendar;

use DateTime;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventData;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

interface EventInterface
{
    public function occurrences() : Collection ;

    public function title() : string;

    public function type() : EventType;

    public function repetitions() : Repetitions;

    public function duration() : Duration;

    public function startDate() : DateTime;

    public function endDate() : DateTime;

    public function move(DateTime $startDate);

    public function isSingle();

    public function isWeekly() : bool;

    public function closeAtDate(DateTime $date);

    public function resize(DateTime $newStartDate = null, DateTime $newEndDate = null, Repetitions $repetitions = null, OccurrenceInterface $occurrence = null);

    public function getOccurrenceById(UuidInterface $occurrenceId) : OccurrenceInterface;

    public function id() : UuidInterface;

    public function calendar();

    public function findPivotDate(OccurrenceInterface $editedOccurrence) : DateTime;

    public function update(EventData $data);
}