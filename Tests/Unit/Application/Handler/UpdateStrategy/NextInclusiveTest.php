<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\UpdateStrategy\NextInclusive;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class NextInclusiveTest extends PHPUnit_Framework_TestCase
{
    public function testFindPivotDate()
    {
        $eventMock = m::mock(Event::class);

        {
            $occurrence1 = new Occurrence(null, new DateTime("2016-09-01 12:00:00"), new Occurrence\Duration(60), $eventMock);
            $occurrence2 = new Occurrence(null, new DateTime("2016-09-02 12:00:00"), new Occurrence\Duration(60), $eventMock);
            $occurrence3 = new Occurrence(null, new DateTime("2016-09-03 12:00:00"), new Occurrence\Duration(60), $eventMock);
            $occurrence4 = new Occurrence(null, new DateTime("2016-09-04 12:00:00"), new Occurrence\Duration(60), $eventMock);
            $occurrence5 = new Occurrence(null, new DateTime("2016-09-05 12:00:00"), new Occurrence\Duration(60), $eventMock);
        }

        $occurrences = [
            $occurrence5,
            $occurrence2,
            $occurrence1,
            $occurrence4,
            $occurrence3,
        ];

        $occurrencesCollectionMock = new ArrayCollection($occurrences);

        $eventMock->shouldReceive("occurrences")->times(5)->andReturn($occurrencesCollectionMock);

        $nextInclusive = new NextInclusive();

        $this->assertEquals($nextInclusive->findPivotDate($occurrence5), $occurrence4->endDate());
        $this->assertEquals($nextInclusive->findPivotDate($occurrence4), $occurrence3->endDate());
        $this->assertEquals($nextInclusive->findPivotDate($occurrence3), $occurrence2->endDate());
        $this->assertEquals($nextInclusive->findPivotDate($occurrence2), $occurrence1->endDate());
        $this->assertEquals($nextInclusive->findPivotDate($occurrence1), $occurrence1->endDate());
    }

    /**
     * @deprecated
     * @todo remove! this should be in Single update strategy test!
     */
    public function testUpdateSingleToSingle() {
        $this->markTestSkipped();
        $eventMock = m::mock(Event::class);

        $oldOccurrenceMock = m::mock(Occurrence::class);
        $oldOccurrenceMock->shouldReceive("synchronizeWithEvent")->once();
        $oldOccurrenceMock->shouldReceive("event")->once()->andReturn($eventMock);

        createsCommand: {
            $command = new UpdateEventCommand();
            $command->type = EventType::TYPE_SINGLE;
            $command->duration = 60;
            $command->startDate = new DateTime("today");
            $command->endDate = new DateTime("today +60 minutes");
            $command->title = "New title";
            $command->method = 'nextinclusive';
            $command->repetitionDays = [];
            $command->occurrence = $oldOccurrenceMock;
        }

        $eventMock->shouldReceive("occurrences->first")->once()->andReturn($oldOccurrenceMock);
        $eventMock->shouldReceive("type->isType")->with('single')->once()->andReturn(true);
        $eventMock->shouldReceive("updateWithCommand")->with($command)->once();

        $eventFactoryMock = m::mock(EventFactoryInterface::class);

        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("update")->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("update")->once()->with($oldOccurrenceMock);

        $nextInclusive = new NextInclusive();
        $nextInclusive->setEventFactory($eventFactoryMock);
        $nextInclusive->setOccurrenceFactory($occurrenceFactoryMock);
        $nextInclusive->setOccurrenceRepository($occurrenceRepositoryMock);
        $nextInclusive->setEventRepository($eventRepositoryMock);
        $nextInclusive->update($command);
    }

    /**
     * @todo move to single test!
     */
    public function testUpdateSingleToWeekly() {
        $this->markTestSkipped();
        $newOccurrencesCollection = new ArrayCollection([]);

        $eventMock = m::mock(Event::class);

        $oldOccurrenceMock = m::mock(Occurrence::class);
        $oldOccurrenceMock->shouldReceive("event")->once()->andReturn($eventMock);

        createsCommand: {
            $command = new UpdateEventCommand();
            $command->type = EventType::TYPE_WEEKLY;
            $command->duration = 60;
            $command->startDate = new DateTime("yesterday");
            $command->endDate = new DateTime("tomorrow");
            $command->title = "New title";
            $command->method = 'nextinclusive';
            $command->repetitionDays = [];
            $command->occurrence = $oldOccurrenceMock;
        }

        $eventMock->shouldReceive("occurrences->first")->once()->andReturn($oldOccurrenceMock);
        $eventMock->shouldReceive("type->isType")->with('single')->once()->andReturn(true);
        $eventMock->shouldReceive("updateWithCommand")->with($command)->once();
        $eventMock->shouldReceive("setOccurrences")->with($newOccurrencesCollection)->once();

        $eventFactoryMock = m::mock(EventFactoryInterface::class);

        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);
        $occurrenceFactoryMock->shouldReceive('generateCollectionFromEvent')->once()->with($eventMock)->andReturn($newOccurrencesCollection);

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("update")->once()->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("remove")->once()->with($oldOccurrenceMock);
        $occurrenceRepositoryMock->shouldReceive("insert")->once()->with($newOccurrencesCollection);

        $nextInclusive = new NextInclusive();
        $nextInclusive->setEventFactory($eventFactoryMock);
        $nextInclusive->setOccurrenceFactory($occurrenceFactoryMock);
        $nextInclusive->setOccurrenceRepository($occurrenceRepositoryMock);
        $nextInclusive->setEventRepository($eventRepositoryMock);
        $nextInclusive->update($command);
    }

    public function testUpdateWeeklyToWeekly()
    {
        $pivotDate = new DateTime("now");
        $newOccurrencesCollection = new ArrayCollection([]);

        /** @var m\MockInterface|Event $originalEventMock */
        $originalEventMock = m::mock(Event::class);

        occurrencesCollection: {
            $oldOccurrenceMock1 = m::mock(Occurrence::class);
            $oldOccurrenceMock1->shouldReceive("endDate")->andReturn(new Datetime("yesterday +90 minutes"));

            $oldOccurrenceMock2 = m::mock(Occurrence::class);
            $oldOccurrenceMock2->shouldReceive("startDate")->andReturn($pivotDate);
            $oldOccurrenceMock2->shouldReceive("endDate")->andReturn(new Datetime("+90 minutes"));
            $oldOccurrenceMock2->shouldReceive("event")->andReturn($originalEventMock);
            $oldOccurrenceMock2->shouldReceive("setDeletedAt")->once();

            $oldOccurrenceMock3 = m::mock(Occurrence::class);
            $oldOccurrenceMock3->shouldReceive("endDate")->andReturn(new Datetime("tomorrow +90 minutes"));
            $oldOccurrenceMock3->shouldReceive("setDeletedAt")->once();

            $oldOccurrencesCollection = new ArrayCollection([
                $oldOccurrenceMock1,
                $oldOccurrenceMock2,
                $oldOccurrenceMock3
            ]);
        }

        $oldNextEventOccurrence = m::mock(Occurrence::class);
        $oldNextEventOccurrence->shouldReceive("setDeletedAt")->once();

        $oldNextEvent = m::mock(Event::class);
        $oldNextEvent->shouldReceive("setDeletedAt")->once();
        $oldNextEvent->shouldReceive("occurrences")->once()->andReturn(new ArrayCollection([
            $oldNextEventOccurrence
        ]));
        $oldNextEvent->shouldReceive("unsetPrevious")->once()->andReturnNull();
        $oldNextEvent->shouldReceive("next")->once()->andReturnNull();

        $newEventMock = m::mock(Event::class);

        $originalEventMock->shouldReceive("changeEndDate")->with($pivotDate);
        $originalEventMock->shouldReceive("occurrences")->andReturn($oldOccurrencesCollection);
        $originalEventMock->shouldReceive("type->isType")->with('single')->andReturn(false);
        $originalEventMock->shouldReceive("type->isType")->with('weekly')->andReturn(true);
        $originalEventMock->shouldReceive("next")->andReturn($oldNextEvent);
        $originalEventMock->shouldReceive("setNext")->with($newEventMock)->once();

        $filteredOccurrences = null;

        $originalEventMock->shouldReceive("setOccurrences")->andReturnUsing(
            function(ArrayCollection $collection) use (&$filteredOccurrences) {
                $filteredOccurrences = $collection;
            }
        );

        $newEventMock->shouldReceive("setOccurrences")->with($newOccurrencesCollection);

        createsCommand: {
            $command = new UpdateEventCommand();
            $command->type = EventType::TYPE_WEEKLY;
            $command->duration = 60;
            $command->startDate = new DateTime("yesterday");
            $command->endDate = new DateTime("tomorrow");
            $command->title = "New title";
            $command->method = 'nextinclusive';
            $command->repetitionDays = [];
            $command->occurrence = $oldOccurrenceMock2;
        }

        $eventFactoryMock = m::mock(EventFactoryInterface::class);
        $eventFactoryMock->shouldReceive("createFromCommand")->andReturn($newEventMock);

        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);
        $occurrenceFactoryMock->shouldReceive("generateCollectionFromEvent")->with($newEventMock)->andReturn($newOccurrencesCollection);

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("update")->with($originalEventMock);
        $eventRepositoryMock->shouldReceive("insert")->with($newEventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("update")->once()->with($oldOccurrencesCollection);

        $nextInclusive = new NextInclusive();
        $nextInclusive->setEventFactory($eventFactoryMock);
        $nextInclusive->setOccurrenceRepository($occurrenceRepositoryMock);
        $nextInclusive->setOccurrenceFactory($occurrenceFactoryMock);
        $nextInclusive->setEventRepository($eventRepositoryMock);
        $nextInclusive->update($command);

        /**
         * todo: dokończyć!
         */
    }

    public function testRemoveWeekly() {
        $pivotDate = new DateTime("now");
        $deletedDate1 = null;
        $deletedDate2 = null;
        $deletedDate3 = null;

        /** @var m\MockInterface|Event $originalEventMock */
        $originalEventMock = m::mock(Event::class);

        occurrencesCollection: {
            $oldOccurrenceMock1 = m::mock(Occurrence::class);
            $oldOccurrenceMock1->shouldReceive("endDate")->times(3)->andReturn(new Datetime("yesterday +90 minutes"));

            $oldOccurrenceMock2 = m::mock(Occurrence::class);
            $oldOccurrenceMock2->shouldReceive("startDate")->times(4)->andReturn($pivotDate);
            $oldOccurrenceMock2->shouldReceive("endDate")->times(2)->andReturn(new Datetime("+90 minutes"));
            $oldOccurrenceMock2->shouldReceive("event")->times(2)->andReturn($originalEventMock);
            $oldOccurrenceMock2->shouldReceive("setDeletedAt")->once()->andReturnUsing(function(DateTime $date) use (&$deletedDate1) {
                $deletedDate1 = $date;
            });

            $oldOccurrenceMock3 = m::mock(Occurrence::class);
            $oldOccurrenceMock3->shouldReceive("endDate")->andReturn(new Datetime("tomorrow +90 minutes"));
            $oldOccurrenceMock3->shouldReceive("setDeletedAt")->once()->andReturnUsing(function(DateTime $date) use (&$deletedDate2) {
                $deletedDate2 = $date;
            });

            $oldOccurrencesCollection = new ArrayCollection([
                $oldOccurrenceMock1,
                $oldOccurrenceMock2,
                $oldOccurrenceMock3
            ]);
        }

        $nextEventOccurrence = m::mock(Occurrence::class);
        $nextEventOccurrence->shouldReceive("setDeletedAt")->once()->andReturnUsing(function(DateTime $date) use (&$deletedDate3) {
            $deletedDate3 = $date;
        });;

        $nextEvent = m::mock(Event::class);
        $nextEvent->shouldReceive("setDeletedAt")->once();
        $nextEvent->shouldReceive("occurrences")->once()->andReturn(new ArrayCollection([
            $nextEventOccurrence
        ]));
        $nextEvent->shouldReceive("unsetPrevious")->once()->andReturnNull();
        $nextEvent->shouldReceive("next")->once()->andReturnNull();

        $originalEventMock->shouldReceive("changeEndDate")->with($pivotDate);
        $originalEventMock->shouldReceive("occurrences")->andReturn($oldOccurrencesCollection);
        $originalEventMock->shouldReceive("type->isType")->with('single')->andReturn(false);
        $originalEventMock->shouldReceive("type->isType")->with('weekly')->andReturn(true);
        $originalEventMock->shouldReceive("next")->once()->andReturn($nextEvent);

        $filteredOccurrences = null;

        $originalEventMock->shouldReceive("setOccurrences")->andReturnUsing(
            function(ArrayCollection $collection) use (&$filteredOccurrences) {
                $filteredOccurrences = $collection;
            }
        );

        createsCommand: {
            $command = new RemoveEventCommand();
            $command->method = 'nextinclusive';
            $command->occurrence = $oldOccurrenceMock2;
        }

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("update")->once()->with($originalEventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("update")->once()->with($oldOccurrencesCollection);

        $nextInclusive = new NextInclusive();
        $nextInclusive->setOccurrenceRepository($occurrenceRepositoryMock);
        $nextInclusive->setEventRepository($eventRepositoryMock);
        $nextInclusive->update($command);

        $this->assertEquals(new DateTime(), $deletedDate1);
        $this->assertEquals(new DateTime(), $deletedDate2);
        $this->assertEquals(new DateTime(), $deletedDate3);

        $this->assertCount(3, $filteredOccurrences);
        $this->assertEquals($oldOccurrenceMock1, $filteredOccurrences[0]);
        $this->assertEquals($oldOccurrenceMock2, $filteredOccurrences[1]);
        $this->assertEquals($oldOccurrenceMock3, $filteredOccurrences[2]);
    }

    public function tearDown()
    {
        m::close();
    }
}
