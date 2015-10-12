<?php
namespace Dende\Calendar\UserInterface\Symfony\CalendarBundle\Tests\Unit\Model;

use DateTime;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Tests\AssertDatesEqualTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EventTest
 * @package Gyman\Domain\Tests\Unit\Model
 */
class OccurrenceFactoryTest extends \PHPUnit_Framework_TestCase
{
    use AssertDatesEqualTrait;

    public function testGenerateOccurencesCollection()
    {
        $event = new Event(
            new EventId(0),
            new Calendar(
                new CalendarId(0),
                'calendar-title'
            ),
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-09-30 13:30:00'),
            'some title',
            new Repetitions([
                Repetitions::MONDAY,
                Repetitions::WEDNESDAY,
                Repetitions::FRIDAY,
            ]),
            new Duration(90),
            new ArrayCollection()
        );

        $collection = OccurrenceFactory::generateCollectionFromEvent($event);

        $this->assertCount(13, $collection);

        $this->assertDatesEqual($collection[0]->startDate(), '2015-09-02 12:00:00');
        $this->assertDatesEqual($collection[1]->startDate(), '2015-09-04 12:00:00');
        $this->assertDatesEqual($collection[2]->startDate(), '2015-09-07 12:00:00');
        $this->assertDatesEqual($collection[3]->startDate(), '2015-09-09 12:00:00');
        $this->assertDatesEqual($collection[4]->startDate(), '2015-09-11 12:00:00');
        $this->assertDatesEqual($collection[5]->startDate(), '2015-09-14 12:00:00');
        $this->assertDatesEqual($collection[6]->startDate(), '2015-09-16 12:00:00');
        $this->assertDatesEqual($collection[7]->startDate(), '2015-09-18 12:00:00');
        $this->assertDatesEqual($collection[8]->startDate(), '2015-09-21 12:00:00');
        $this->assertDatesEqual($collection[9]->startDate(), '2015-09-23 12:00:00');
        $this->assertDatesEqual($collection[10]->startDate(), '2015-09-25 12:00:00');
        $this->assertDatesEqual($collection[11]->startDate(), '2015-09-28 12:00:00');
        $this->assertDatesEqual($collection[12]->startDate(), '2015-09-30 12:00:00');
    }

    public function testProduceEveryDayOccurrence()
    {
        $event = new Event(
            new EventId(0),
            new Calendar(
                new CalendarId(0),
                'calendar-title'
            ),
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-09-30 13:30:00'),
            'some title',
            new Repetitions([
                Repetitions::MONDAY,
                Repetitions::TUESDAY,
                Repetitions::WEDNESDAY,
                Repetitions::THURSDAY,
                Repetitions::FRIDAY,
                Repetitions::SATURDAY,
                Repetitions::SUNDAY,
            ]),
            new Duration(90),
            new ArrayCollection()
        );

        $collection = OccurrenceFactory::generateCollectionFromEvent($event);

        $this->assertCount(30, $collection);

        $this->assertDatesEqual($collection[0]->startDate(), '2015-09-01 12:00:00');
        $this->assertDatesEqual($collection[9]->startDate(), '2015-09-10 12:00:00');
        $this->assertDatesEqual($collection[29]->startDate(), '2015-09-30 12:00:00');
    }

    public function testProduceNoOccurrenceWeekly()
    {
        $event = new Event(
            new EventId(0),
            new Calendar(
                new CalendarId(0),
                'calendar-title'
            ),
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-09-01 12:00:00'),
            'some title',
            new Repetitions([
                Repetitions::TUESDAY,
            ]),
            new Duration(90),
            new ArrayCollection()
        );

        $this->assertCount(0, OccurrenceFactory::generateCollectionFromEvent($event));

        $event = new Event(
            new EventId(0),
            new Calendar(
                new CalendarId(0),
                'calendar-title'
            ),
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-09-01 13:30:00'),
            'some title',
            new Repetitions([]),
            new Duration(90),
            new ArrayCollection()
        );

        $this->assertCount(0, OccurrenceFactory::generateCollectionFromEvent($event));
    }
}
