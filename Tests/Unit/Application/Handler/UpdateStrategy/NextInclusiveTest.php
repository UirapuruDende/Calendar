<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Handler\UpdateStrategy\NextInclusive;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class NextInclusiveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider findingPivotDataProvider
     */
    public function test_finding_pivot_date(int $clickedIndex, int $expectedPivotDateIndex)
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

        $pivotDate = (new NextInclusive())->findPivotDate($event->occurrences()->toArray()[$clickedIndex], $event);

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
     * @test
     */
    public function it_tests_extending_event()
    {
        setup : {
            $calendar = Calendar::create('');
            $eventId  = EventId::create();
            $baseDate = new DateTime('last monday 12:00:00');

            $calendar->addEvent(
                $eventId,
                'title',
                $baseDate,
                (clone $baseDate)->modify('+6 days +2 hours'),
                EventType::weekly(),
                Repetitions::workingDays()
            );
        }

        $event = $calendar->getEventById($eventId);

        $eventRepository = new InMemoryEventRepository();
        $eventRepository->insert($event);

        $occurrenceRepository = new InMemoryOccurrenceRepository($event->occurrences());

        $this->assertCount(5, $event->occurrences());

        $clickedOccurrence = $event->occurrences()->get(3);
        $occurrenceRepository->insert($clickedOccurrence);

        createsCommand: {
            $command               = new UpdateEventCommand();
            $command->startDate    = clone $baseDate;
            $command->endDate      = (clone $baseDate)->modify('+9 days +1 hour');
            $command->title        = 'New title';
            $command->method       = 'nextinclusive';
            $command->repetitions  = Repetitions::workingDays()->getArray();
            $command->occurrenceId = $clickedOccurrence->id()->__toString();
        }

        $nextInclusive = new NextInclusive();
        $nextInclusive->setOccurrenceRepository($occurrenceRepository);
        $nextInclusive->setEventRepository($eventRepository);
        $nextInclusive->update($command);

        $this->assertCount(2, $eventRepository->findAll());
        $this->assertCount(2, $calendar->events());

        /** @var Event $newEvent */
        $newEvent = $calendar->events()->last();

        $pivotDate = $event->occurrences()->get(3)->endDate();

        $this->markTestIncomplete();

        $this->assertEquals($pivotDate, $newEvent->startDate());
        $this->assertEquals($newEndDate, $newEvent->endDate());

        $occurrences = $occurrenceRepository->findAll();

        $this->assertCount(6, $occurrences);

        /** @var Occurrence[]|array $result */
        $result = array_values($occurrences->getIterator()->getArrayCopy());

        // OLD ONES

        $this->assertEquals($baseDate, $result[0]->startDate());
        $this->assertEquals((clone $baseDate)->modify('+2 hours'), $result[0]->endDate());
        $this->assertEquals(null, $result[0]->getDeletedAt());

        $this->assertEquals((clone $baseDate)->modify('+1 days'), $result[1]->startDate());
        $this->assertEquals((clone $baseDate)->modify('+1 days +2 hours'), $result[1]->endDate());
        $this->assertEquals(null, $result[1]->getDeletedAt());

        $this->assertEquals((clone $baseDate)->modify('+2 days'), $result[2]->startDate());
        $this->assertEquals((clone $baseDate)->modify('+2 days +2 hours'), $result[2]->endDate());
        $this->assertEquals(null, $result[2]->getDeletedAt());

        // OLD ONES DELETED

        $this->assertEquals((clone $baseDate)->modify('+3 days'), $result[3]->startDate());
        $this->assertEquals((clone $baseDate)->modify('+3 days +2 hours'), $result[3]->endDate());
        $this->assertEquals(null, $result[3]->getDeletedAt());

        $this->assertEquals((clone $baseDate)->modify('+4 days'), $result[4]->startDate());
        $this->assertEquals((clone $baseDate)->modify('+4 days +2 hours'), $result[4]->endDate());
        $this->assertEquals(new DateTime('now'), $result[4]->getDeletedAt());

        // NEW ONES

        $this->assertEquals((clone $baseDate)->modify('+3 days'), $result[5]->startDate());
        $this->assertEquals((clone $baseDate)->modify('+3 days +1 hours'), $result[5]->endDate());
        $this->assertEquals(null, $result[5]->getDeletedAt());

        $this->assertEquals((clone $baseDate)->modify('+4 days'), $result[6]->startDate());
        $this->assertEquals((clone $baseDate)->modify('+4 days +1 hours'), $result[6]->endDate());
        $this->assertEquals(null, $result[6]->getDeletedAt());

        // NEW ONES
    }

    public function testRemove()
    {
        $this->markTestIncomplete();
        $pivotDate = new DateTime('now');

        $event = new Event(EventId::create(), Calendar::create(''), EventType::weekly(), new DateTime(), new DateTime(), 'title', Repetitions::workingDays());

        occurrencesCollection: {
            $occurrence1 = new Occurrence(OccurrenceId::create(), $event, new DateTime('-1 day'), new OccurrenceDuration(90));
            $occurrence2 = new Occurrence(OccurrenceId::create(), $event, new DateTime('-2 hours'), new OccurrenceDuration(90));
            $occurrence3 = new Occurrence(OccurrenceId::create(), $event, new DateTime('+1 day'), new OccurrenceDuration(90));

            $oldOccurrencesCollection = new ArrayCollection([$occurrence1, $occurrence2, $occurrence3]);
        }

        $calendar = Calendar::create('test');

        $originalEventMock = new Event(EventId::create(), $calendar, EventType::weekly(), new DateTime('yesterday'), new DateTime('tomorrow'), 'title', Repetitions::workingDays(), $oldOccurrencesCollection);

        createsCommand: {
            $command             = new RemoveEventCommand();
            $command->method     = 'nextinclusive';
            $command->occurrence = $occurrence2;
        }

        repositoriesMocks: {
            $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
            $eventRepositoryMock->shouldReceive('update')->once()->with($originalEventMock);
            $eventRepositoryMock->shouldReceive('findOneByOccurrence')->once()->with($occurrence2)->andReturn($originalEventMock);

            $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
            $occurrenceRepositoryMock->shouldReceive('update')->once()->with($oldOccurrencesCollection);
        }

        $nextInclusive = new NextInclusive();
        $nextInclusive->setOccurrenceRepository($occurrenceRepositoryMock);
        $nextInclusive->setEventRepository($eventRepositoryMock);
        $nextInclusive->update($command);

        $this->assertNull($occurrence1->getDeletedAt());
        $this->assertEquals(new DateTime(), $occurrence2->getDeletedAt());
        $this->assertEquals(new DateTime(), $occurrence3->getDeletedAt());
    }

    public function tearDown()
    {
        m::close();
    }
}
