<?php
namespace Dende\Calendar\Domain\Calendar;

use DateTime;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventData;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\IdInterface;
use Dende\Calendar\Domain\SoftDeleteable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface EventInterface
{

    public static function getOccurrenceFactory() : OccurrenceFactoryInterface;

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

    public function getOccurrenceById(IdInterface $occurrenceId) : OccurrenceInterface;

    public function id();

    public function calendar();

    public function findPivotDate(OccurrenceInterface $editedOccurrence) : DateTime;

    public function update(EventData $data);

    public static function setFactoryClass(string $class);
}