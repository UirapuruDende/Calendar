<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Handler\UpdateStrategy\NextInclusive;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;

class NextInclusiveTest extends UpdateStrategyTestCase
{
    /**
     * @test
     */
    public function it_tests_extending_event()
    {
        $this->markTestIncomplete();
        
        $this->given_I_have_weekly_event();
        $this->when_I_edit_inclusively_event(
            $this->event->startDate(),
            (clone $this->event->startDate())->modify('+9 days +1 hour'),
            Repetitions::weekendDays(),
            $this->event->occurrences()->get(3)
        );
        $this->then_I_have_two_modified_and_connected_events();
        $this->and_I_have_modified_occurrences();

//
//        $this->assertCount(6, $occurrences);
//
//        /** @var Occurrence[]|array $result */
//        $result = array_values($occurrences->getIterator()->getArrayCopy());
//
//        // OLD ONES
//
//        $this->assertEquals($baseDate, $result[0]->startDate());
//        $this->assertEquals((clone $baseDate)->modify('+2 hours'), $result[0]->endDate());
//        $this->assertEquals(null, $result[0]->getDeletedAt());
//
//        $this->assertEquals((clone $baseDate)->modify('+1 days'), $result[1]->startDate());
//        $this->assertEquals((clone $baseDate)->modify('+1 days +2 hours'), $result[1]->endDate());
//        $this->assertEquals(null, $result[1]->getDeletedAt());
//
//        $this->assertEquals((clone $baseDate)->modify('+2 days'), $result[2]->startDate());
//        $this->assertEquals((clone $baseDate)->modify('+2 days +2 hours'), $result[2]->endDate());
//        $this->assertEquals(null, $result[2]->getDeletedAt());
//
//        // OLD ONES DELETED
//
//        $this->assertEquals((clone $baseDate)->modify('+3 days'), $result[3]->startDate());
//        $this->assertEquals((clone $baseDate)->modify('+3 days +2 hours'), $result[3]->endDate());
//        $this->assertEquals(null, $result[3]->getDeletedAt());
//
//        $this->assertEquals((clone $baseDate)->modify('+4 days'), $result[4]->startDate());
//        $this->assertEquals((clone $baseDate)->modify('+4 days +2 hours'), $result[4]->endDate());
//        $this->assertEquals(new DateTime('now'), $result[4]->getDeletedAt());
//
//        // NEW ONES
//
//        $this->assertEquals((clone $baseDate)->modify('+3 days'), $result[5]->startDate());
//        $this->assertEquals((clone $baseDate)->modify('+3 days +1 hours'), $result[5]->endDate());
//        $this->assertEquals(null, $result[5]->getDeletedAt());
//
//        $this->assertEquals((clone $baseDate)->modify('+4 days'), $result[6]->startDate());
//        $this->assertEquals((clone $baseDate)->modify('+4 days +1 hours'), $result[6]->endDate());
//        $this->assertEquals(null, $result[6]->getDeletedAt());
//
//        // NEW ONES
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

    }

    private function given_I_have_weekly_event()
    {
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

        $event = $calendar->getEventById($eventId);

        $this->eventRepository->insert($event);
        $this->occurrenceRepository->mergeCollection($event->occurrences());

        $this->event = $event;
    }

    private function when_I_edit_inclusively_event(DateTime $startDate, DateTime $endDate, Repetitions $repetitions, Occurrence $occurrence)
    {
        $command               = new UpdateEventCommand();
        $command->startDate    = clone $startDate;
        $command->endDate      = clone $endDate;
        $command->title        = $this->event->title();
        $command->method       = 'nextinclusive';
        $command->repetitions  = $repetitions->getArray();
        $command->occurrenceId = $occurrence->id()->__toString();

        $this->updateEventHandler->handle($command);
    }

    private function then_I_have_two_modified_and_connected_events()
    {
        $events = $this->eventRepository->findAll();
        $this->assertCount(2, $events);
        $this->assertCount(2, $this->event->calendar()->events());

//        /** @var Event $newEvent */
//        $newEvent = $calendar->events()->last();
//
//        $pivotDate = $event->occurrences()->get(3)->endDate();
    }

    private function and_I_have_modified_occurrences()
    {

    }
}
