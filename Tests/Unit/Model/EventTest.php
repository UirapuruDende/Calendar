<?php
namespace Dende\Calendar\Tests\Unit\Model;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Tests\AssertDatesEqualTrait;

/**
 * Class EventTest.
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    use AssertDatesEqualTrait;

    public function testCalculateOccurrencesDatesWeekly()
    {
        $event = new Event(
            0,
            new Calendar(
                0,
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
            null
        );

        $collection = $event->calculateOccurrencesDates();
        $this->assertCount(13, $collection);

        $this->assertDatesEqual($collection[0], '2015-09-02 12:00:00');
        $this->assertDatesEqual($collection[1], '2015-09-04 12:00:00');
        $this->assertDatesEqual($collection[2], '2015-09-07 12:00:00');
        $this->assertDatesEqual($collection[3], '2015-09-09 12:00:00');
        $this->assertDatesEqual($collection[4], '2015-09-11 12:00:00');
        $this->assertDatesEqual($collection[5], '2015-09-14 12:00:00');
        $this->assertDatesEqual($collection[6], '2015-09-16 12:00:00');
        $this->assertDatesEqual($collection[7], '2015-09-18 12:00:00');
        $this->assertDatesEqual($collection[8], '2015-09-21 12:00:00');
        $this->assertDatesEqual($collection[9], '2015-09-23 12:00:00');
        $this->assertDatesEqual($collection[10], '2015-09-25 12:00:00');
        $this->assertDatesEqual($collection[11], '2015-09-28 12:00:00');
        $this->assertDatesEqual($collection[12], '2015-09-30 12:00:00');
    }

    public function testCalculateOccurrencesDatesSingle()
    {
        $event = new Event(
            0,
            new Calendar(
                0,
                'calendar-title'
            ),
            new EventType(EventType::TYPE_SINGLE),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-09-30 13:30:00'),
            'some title',
            new Repetitions([]),
            new Duration(90),
            null
        );

        $collection = $event->calculateOccurrencesDates();
        $this->assertCount(1, $collection);

        $this->assertDatesEqual($collection[0], '2015-09-01 12:00:00');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage End date '2015-08-01 12:00:00' cannot be before start date '2015-09-01 12:00:00'
     */
    public function testConstructorExceptions()
    {
        $event = new Event(
            0,
            new Calendar(
                0,
                'calendar-title'
            ),
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-08-01 12:00:00'),
            'some title',
            new Repetitions([
                Repetitions::TUESDAY,
            ]),
            new Duration(90),
            null
        );
    }
}
