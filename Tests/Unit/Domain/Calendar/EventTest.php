<?php
namespace Dende\Calendar\Tests\Unit\Domain\Calendar;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use PHPUnit_Framework_TestCase;

class EventTest extends PHPUnit_Framework_TestCase
{
    public function testCalculateOccurrencesDatesWeekly()
    {
        $event = new Event(
            EventId::create(),
            Calendar::create('title'),
            EventType::weekly(),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-09-30 13:30:00'),
            'some title',
            new Repetitions([
                Repetitions::MONDAY,
                Repetitions::WEDNESDAY,
                Repetitions::FRIDAY,
            ])
        );

        $occurrences = $event->occurrences();

        $this->assertCount(13, $occurrences);

        $this->assertEquals($occurrences[0]->startDate(), new DateTime('2015-09-02 12:00:00'));
        $this->assertEquals($occurrences[1]->startDate(), new DateTime('2015-09-04 12:00:00'));
        $this->assertEquals($occurrences[2]->startDate(), new DateTime('2015-09-07 12:00:00'));
        $this->assertEquals($occurrences[3]->startDate(), new DateTime('2015-09-09 12:00:00'));
        $this->assertEquals($occurrences[4]->startDate(), new DateTime('2015-09-11 12:00:00'));
        $this->assertEquals($occurrences[5]->startDate(), new DateTime('2015-09-14 12:00:00'));
        $this->assertEquals($occurrences[6]->startDate(), new DateTime('2015-09-16 12:00:00'));
        $this->assertEquals($occurrences[7]->startDate(), new DateTime('2015-09-18 12:00:00'));
        $this->assertEquals($occurrences[8]->startDate(), new DateTime('2015-09-21 12:00:00'));
        $this->assertEquals($occurrences[9]->startDate(), new DateTime('2015-09-23 12:00:00'));
        $this->assertEquals($occurrences[10]->startDate(), new DateTime('2015-09-25 12:00:00'));
        $this->assertEquals($occurrences[11]->startDate(), new DateTime('2015-09-28 12:00:00'));
        $this->assertEquals($occurrences[12]->startDate(), new DateTime('2015-09-30 12:00:00'));
    }

    public function testCalculateOccurrencesDatesSingle()
    {
        $event = new Event(
            EventId::create(),
            Calendar::create('test'),
            EventType::single(),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-09-30 13:30:00'),
            'some title',
            new Repetitions([])
        );

        $occurrences = $event->occurrences();

        $this->assertCount(1, $occurrences);
        $this->assertEquals($occurrences[0]->startDate(), new DateTime('2015-09-01 12:00:00'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage End date '2015-08-01 12:00:00' cannot be before start date '2015-09-01 12:00:00'
     */
    public function testConstructorExceptions()
    {
        new Event(
            EventId::create(),
            Calendar::create('test'),
            EventType::weekly(),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-08-01 12:00:00'),
            'some title',
            new Repetitions([
                Repetitions::TUESDAY,
            ])
        );
    }

    /**
     * @dataProvider closeAtDateDataProvider
     *
     * @param DateTime $closingDate
     * @param array    $expected
     */
    public function testCloseAtDate(DateTime $closingDate, array $expected)
    {
        $event = new Event(
            EventId::create(),
            Calendar::create('test'),
            EventType::weekly(),
            new DateTime('last monday 12:00:00'),
            (new DateTime('last monday 12:30:00'))->modify('+6 days'),
            'some title',
            new Repetitions([1, 3, 5])
        );

        $this->assertCount(3, $event->occurrences());

        $event->closeAtDate($closingDate);

        $this->assertEquals($closingDate, $event->endDate(), null, 2);

        $this->assertEquals($expected[0], $event->occurrences()->get(0)->getDeletedAt(), null, 2);
        $this->assertEquals($expected[1], $event->occurrences()->get(1)->getDeletedAt(), null, 2);
        $this->assertEquals($expected[2], $event->occurrences()->get(2)->getDeletedAt(), null, 2);
    }

    public function closeAtDateDataProvider() : array
    {
        $base = new DateTime('last monday 12:00:00');

        return [
            'before first' => [
                'closingDate' => (clone $base)->modify('-1 day'),
                'expected'    => [
                    new DateTime(),
                    new DateTime(),
                    new DateTime(),
                ],
            ],
            'tuesday' => [
                'closingDate' => (clone $base)->modify('+1 day'),
                'expected'    => [
                    null,
                    new DateTime(),
                    new DateTime(),
                ],
            ],
            'thursday' => [
                'closingDate' => (clone $base)->modify('+3 day'),
                'expected'    => [
                    null,
                    null,
                    new DateTime(),
                ],
            ],
            'last' => [
                'closingDate' => (clone $base)->modify('+5 day'),
                'expected'    => [
                    null,
                    null,
                    null,
                ],
            ],
        ];
    }
}
