<?php
namespace Dende\Calendar\Tests\Unit\Domain\Calendar;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit_Framework_TestCase;
use Ramsey\Uuid\Uuid;

class EventTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_tests_resizing_both_sides()
    {
        $base = Carbon::instance(new DateTime('last monday 12:00:00'));

        /** @var OccurrenceFactoryInterface $factory */
        $factory = new Event::$occurrenceFactoryClass;

        $collection = new ArrayCollection();

        $event = new Event(
            EventId::create(),
            Calendar::create('title'),
            EventType::weekly(),
            $base->copy(),
            $base->copy()->addDays(6)->addHours(2),
            'some title',
            new Repetitions([
                Repetitions::MONDAY,
                Repetitions::WEDNESDAY,
                Repetitions::FRIDAY,
            ]),
            $collection
        );

        $occurrenceId1 = OccurrenceId::create();
        $occurrenceId2 = OccurrenceId::create();
        $occurrenceId3 = OccurrenceId::create();

        $collection->add($factory->createFromArray([
            "occurrenceId" => $occurrenceId1,
            "startDate" => $base->copy(),
            "duration"  => new OccurrenceDuration($event->duration()->minutes()),
            "event" =>  $event
        ]));

        $collection->add($factory->createFromArray([
            "occurrenceId" => $occurrenceId2,
            "startDate" => $base->copy()->addDays(2),
            "duration"  => new OccurrenceDuration($event->duration()->minutes()),
            "event" =>  $event
        ]));

        $collection->add($factory->createFromArray([
            "occurrenceId" => $occurrenceId3,
            "startDate" => $base->copy()->addDays(4),
            "duration"  => new OccurrenceDuration($event->duration()->minutes()),
            "event" =>  $event
        ]));

        $event->resize(
            $event->startDate()->copy()->subDays(7),
            $event->endDate()->copy()->addDays(7),
            Repetitions::create([
                Repetitions::MONDAY,
                Repetitions::FRIDAY,
            ])
        );

        $this->assertCount(6, $collection);

        $this->assertEquals($collection[0]->startDate(), $base->copy()->subDays(7));

        $this->assertEquals($collection[1]->startDate(), $base->copy()->subDays(3));

        $this->assertEquals($collection[2]->startDate(), $base);
        $this->assertEquals($collection[2]->id(), $occurrenceId1);

        $this->assertEquals($collection[3]->startDate(), $base->copy()->addDays(4));
        $this->assertEquals($collection[3]->id(), $occurrenceId3);

        $this->assertEquals($collection[4]->startDate(), $base->copy()->addDays(7));

        $this->assertEquals($collection[5]->startDate(), $base->copy()->addDays(11));
    }

    /**
     * @test
     */
    public function it_tests_shrinking_both_sides()
    {
        $base = Carbon::instance(new DateTime('last monday 12:00:00'));

        $collection = new ArrayCollection();

        $event = new Event(
            EventId::create(),
            Calendar::create('title'),
            EventType::weekly(),
            $base->copy(),
            $base->copy()->addDays(20)->addHours(2),
            'some title',
            new Repetitions([
                Repetitions::MONDAY,
                Repetitions::WEDNESDAY,
                Repetitions::FRIDAY,
            ]),
            $collection
        );

        $event->resize();
        $this->assertCount(9, $collection);

        $ids = $collection->map(function(Occurrence $occurrence){ return $occurrence->id(); })->toArray();

        $event->resize(
            $event->startDate()->copy()->addDays(7),
            $event->endDate()->copy()->subDays(7),
            Repetitions::workingDays()
        );

        $this->assertCount(5, $collection);

        $this->assertEquals($collection[0]->id(), $ids[3]);
        $this->assertEquals($collection[2]->id(), $ids[4]);
        $this->assertEquals($collection[4]->id(), $ids[5]);

        for($days = 7; $days<12; $days++) {
            $this->assertEquals($collection[0]->startDate(), $base->copy()->addDays(7));
            $this->assertEquals($collection[0]->endDate(), $base->copy()->addDays(7)->addHours(2));
        }
    }

    public function testCalculateOccurrencesDatesWeekly()
    {
        $this->markTestIncomplete();

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
        $this->markTestIncomplete();

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
     * @test
     * @dataProvider findingPivotDataProvider
     * @param int $clickedIndex
     * @param int $expectedPivotDateIndex
     */
    public function finding_pivot_date(int $clickedIndex, int $expectedPivotDateIndex)
    {
        $event = new Event(
            EventId::create(),
            Calendar::create(''),
            EventType::weekly(),
            new DateTime('last monday 12:00:00'),
            (new DateTime('last monday 12:01:00'))->modify('+6 days'),
            'title',
            Repetitions::workingDays()
        );

        $this->assertCount(5, $event->occurrences());

        $pivotDate = $event->findPivotDate($event->occurrences()[$clickedIndex]);

        $this->assertEquals($event->occurrences()->toArray()[$expectedPivotDateIndex]->endDate(), $pivotDate);
    }

    public function findingPivotDataProvider() : array
    {
        return [
            ['clickedIndex' => 0, 'expectedPivotDateIndex' => 0],
            ['clickedIndex' => 1, 'expectedPivotDateIndex' => 0],
            ['clickedIndex' => 2, 'expectedPivotDateIndex' => 1],
            ['clickedIndex' => 3, 'expectedPivotDateIndex' => 2],
            ['clickedIndex' => 4, 'expectedPivotDateIndex' => 3],
        ];
    }

    /**
     * @dataProvider closeAtDateDataProvider
     *
     * @param array $id
     * @param DateTime $closingDate
     * @param array $expected
     * @param int $expectedCount
     */
    public function testCloseAtDate(array $id, DateTime $closingDate, array $expected)
    {
        $this->markTestIncomplete();

        $baseDate  = Carbon::instance(new DateTime('last monday 12:00:00'));
        $occurrences = new ArrayCollection();

        $factory = new Event::$occurrenceFactoryClass();

        $event = new Event(
            EventId::create(),
            Calendar::create('test'),
            EventType::weekly(),
            $baseDate,
            $baseDate->copy()->addDays(6)->addMinutes(30),
            'some title',
            new Repetitions([Repetitions::MONDAY, Repetitions::WEDNESDAY, Repetitions::FRIDAY]),
            $occurrences
        );

        $occurrences->add($factory->createFromArray([
            'occurrenceId' => OccurrenceId::create($id[0]),
            'startDate' => $baseDate->copy(),
            'duration'  => new OccurrenceDuration(30),
            'event'     => $event,
        ]));

        $occurrences->add($factory->createFromArray([
            'occurrenceId' => OccurrenceId::create($id[1]),
            'startDate' => $baseDate->copy()->addDays(2),
            'duration'  => new OccurrenceDuration(30),
            'event'     => $event,
        ]));

        $occurrences->add($factory->createFromArray([
            'occurrenceId' => OccurrenceId::create($id[2]),
            'startDate' => $baseDate->copy()->addDays(4),
            'duration'  => new OccurrenceDuration(30),
            'event'     => $event,
        ]));

        $this->assertCount(3, $event->occurrences());

        $event->closeAtDate($closingDate);

        $this->assertEquals($closingDate, $event->endDate());

        $this->assertCount(count($expected), $event->occurrences());

    }

    public function closeAtDateDataProvider() : array
    {
        $base = Carbon::instance(new DateTime('last monday 12:00:00'));

        $id1 = Uuid::uuid4();
        $id2 = Uuid::uuid4();
        $id3 = Uuid::uuid4();

        $idsArray = [$id1, $id2, $id3];

        return [
            'before monday' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(-1),
                'expected'    => [],
            ],
            'tuesday' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(1),
                'expected'    => [$id1],
            ],
            'wednesday on time' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(2),
                'expected'    => [],
            ],
            'thursday' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(3),
                'expected'    => [],
            ],
            'last' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(5),
                'expected'    => [],
            ],
        ];
    }
}
