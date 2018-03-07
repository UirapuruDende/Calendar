<?php
namespace Dende\Calendar\Tests\Domain\Calendar;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class EventTest extends TestCase
{
    /**
     * @test
     */
    public function it_tests_resizing_both_sides()
    {
        $base = Carbon::instance(new DateTime('last monday 12:00:00'));

        $collection = new ArrayCollection();

        $event = new Event(
            Uuid::uuid4(),
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

        $occurrenceId1 = Uuid::uuid4();
        $occurrenceId2 = Uuid::uuid4();
        $occurrenceId3 = Uuid::uuid4();

        $collection->add(Occurrence::create(
            $occurrenceId1,
            $base->copy(),
            $event
        ));

        $collection->add(Occurrence::create(
            $occurrenceId2,
            $base->copy()->addDays(2),
            $event
        ));

        $collection->add(Occurrence::create(
            $occurrenceId3,
            $base->copy()->addDays(4),
            $event
        ));

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
            Uuid::uuid4(),
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

        $ids = $collection->map(function (Occurrence $occurrence) {
            return $occurrence->id();
        })->toArray();

        $event->resize(
            $event->startDate()->copy()->addDays(7),
            $event->endDate()->copy()->subDays(7),
            Repetitions::workingDays()
        );

        $this->assertCount(5, $collection);

        $this->assertEquals($collection[0]->id(), $ids[3]);
        $this->assertEquals($collection[2]->id(), $ids[4]);
        $this->assertEquals($collection[4]->id(), $ids[5]);

        for ($days = 7; $days < 12; ++$days) {
            $this->assertEquals($collection[0]->startDate(), $base->copy()->addDays(7));
            $this->assertEquals($collection[0]->endDate(), $base->copy()->addDays(7)->addHours(2));
        }
    }

    /**
     * @test
     */
    public function it_tests_extending_right_side_with_pivot()
    {
        $base = Carbon::instance(new DateTime('last monday 12:00:00'));

        $collection = new ArrayCollection();

        $event = new Event(
            Uuid::uuid4(),
            Calendar::create('title'),
            EventType::weekly(),
            $base->copy(),
            $base->copy()->addDays(6)->addHours(2),
            'some title',
            Repetitions::workingDays(),
            $collection
        );

        $collection->add(Occurrence::create(
            null,
            $base->copy(),
            $event
        ));

        $collection->add(Occurrence::create(
            null,
            $base->copy()->addDays(1),
            $event
        ));

        $collection->add(Occurrence::create(
            null,
           $base->copy()->addDays(2),
           $event
        ));

        $collection->add(Occurrence::create(
            null,
            $base->copy()->addDays(3),
            $event
        ));

        $collection->add(Occurrence::create(
            null,
           $base->copy()->addDays(4),
           $event
       ));

        $ids = $collection->map(function (Occurrence $occurrence) {
            return $occurrence->id();
        })->toArray();

        $event->resize(
            $event->startDate()->copy(),
            $event->endDate()->copy()->addDays(4),
            new Repetitions([
                Repetitions::MONDAY,
                Repetitions::WEDNESDAY,
                Repetitions::FRIDAY,
            ]),
            $event->occurrences()->get(3)
        );

        $collection = $event->occurrences();

        $this->assertCount(6, $collection);

        $this->assertEquals($base->copy(),                    $collection[0]->startDate());
        $this->assertEquals($base->copy()->addDays(1),  $collection[1]->startDate());
        $this->assertEquals($base->copy()->addDays(2),  $collection[2]->startDate());
        $this->assertEquals($base->copy()->addDays(4),  $collection[3]->startDate());
        $this->assertEquals($base->copy()->addDays(7),  $collection[4]->startDate());
        $this->assertEquals($base->copy()->addDays(9), $collection[5]->startDate());

        $this->assertEquals($collection[0]->id(), $ids[0]);
        $this->assertEquals($collection[1]->id(), $ids[1]);
        $this->assertEquals($collection[2]->id(), $ids[2]);

        $this->assertNotEquals($collection[3]->id(), $ids[3]);
        $this->assertNotEquals($collection[4]->id(), $ids[4]);
    }

    public function testCalculateOccurrencesDatesWeekly()
    {
        $event = new Event(
            Uuid::uuid4(),
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
            Uuid::uuid4(),
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
    public function test_swapped_dates_in_constructor()
    {
        new Event(
            Uuid::uuid4(),
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
     * @expectedException \Exception
     * @expectedExceptionMessage Weekly repeated event must have at least one repetition
     */
    public function test_no_repetitions_with_weekly_in_constructor()
    {
        new Event(
            Uuid::uuid4(),
            Calendar::create('test'),
            EventType::weekly(),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-10-01 12:05:00'),
            'some title',
            new Repetitions()
        );
    }

    /**
     * @test
     * @dataProvider findingPivotDataProvider
     */
    public function finding_pivot_date(int $clickedIndex, int $expectedPivotDateIndex)
    {
        $event = new Event(
            Uuid::uuid4(),
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
     */
    public function testCloseAtDate(array $id, DateTime $closingDate, array $expected)
    {
        $baseDate    = Carbon::instance(new DateTime('last monday 12:00:00'));
        $occurrences = new ArrayCollection();

        $event = new Event(
            Uuid::uuid4(),
            Calendar::create('test'),
            EventType::weekly(),
            $baseDate,
            $baseDate->copy()->addDays(6)->addMinutes(30),
            'some title',
            new Repetitions([Repetitions::MONDAY, Repetitions::WEDNESDAY, Repetitions::FRIDAY]),
            $occurrences
        );

        $occurrences->add(Occurrence::create(
            $id[0],
            $baseDate->copy(),
            $event
        ));

        $occurrences->add(Occurrence::create(
            $id[1],
            $baseDate->copy()->addDays(2),
            $event
        ));

        $occurrences->add(Occurrence::create(
            $id[2],
            $baseDate->copy()->addDays(4),
            $event
        ));

        $event->closeAtDate($closingDate);

        $this->assertCount(count($expected), $event->occurrences());
        $this->assertEquals($closingDate, $event->endDate());

        foreach ($expected as $key => $occurrenceId) {
            $this->assertNotNull($event->getOccurrenceById($occurrenceId), sprintf('Id[%d] %s not found', $key, $occurrences));
        }
    }

    public function closeAtDateDataProvider() : array
    {
        $base = Carbon::instance(new DateTime('last monday 12:00:00'));

        $occurrenceId1 = Uuid::uuid4();
        $occurrenceId2 = Uuid::uuid4();
        $occurrenceId3 = Uuid::uuid4();

        $idsArray = [$occurrenceId1, $occurrenceId2, $occurrenceId3];

        return [
            'before monday' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(-1),
                'expected'    => [],
            ],
            'tuesday' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(1),
                'expected'    => [$occurrenceId1],
            ],
            'wednesday on time' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(2),
                'expected'    => [$occurrenceId1],
            ],
            'thursday' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(3),
                'expected'    => [$occurrenceId1, $occurrenceId2],
            ],
            'last' => [
                'id'          => $idsArray,
                'closingDate' => $base->copy()->addDays(5),
                'expected'    => [$occurrenceId1, $occurrenceId2, $occurrenceId3],
            ],
        ];
    }

    /**
     * @test
     */
    public function pivot_set_out_of_range()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageRegExp('@Pivot \(.+\) must be between startDate \(.+\) and endDate \(.+\)!@ui');

        $baseDate = Carbon::instance(new DateTime('last monday 12:00:00'));

        $occurrences = new ArrayCollection();

        $event = new Event(
            Uuid::uuid4(),
            Calendar::create('test'),
            EventType::weekly(),
            $baseDate,
            $baseDate->copy()->addDays(6)->addMinutes(30),
            'some title',
            new Repetitions([Repetitions::MONDAY, Repetitions::WEDNESDAY, Repetitions::FRIDAY]),
            $occurrences
        );

        $pivotOccurrence = Occurrence::create(
            null,
            $baseDate->copy()->subDays(7),
            $event
        );

        $occurrences->add($pivotOccurrence);

        $occurrences->add(Occurrence::create(
            null,
            $baseDate->copy()->addDays(2),
            $event
        ));

        $occurrences->add(Occurrence::create(
            null,
            $baseDate->copy()->addDays(4),
            $event
        ));

        $event->resize(null, $baseDate->copy()->addDays(7), null, $pivotOccurrence);
    }
}
