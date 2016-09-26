<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\UpdateStrategy\Single;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\Duration as OccurrenceDuration;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * Class EventTest
 * @package Gyman\Domain\Tests\Unit\Model
 */
class SingleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testUpdateSingleToSingle()
    {
        $calendarMock = m::mock(Calendar::class);

        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("isType")->once()->with(EventType::TYPE_SINGLE)->andReturn(true);

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive("event")->once()->andReturn($eventMock);
        $occurrenceMock->shouldReceive("synchronizeWithEvent")->once();

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("update")->once()->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("update")->once()->with($occurrenceMock);

        $command = new UpdateEventCommand();
        $command->type = EventType::TYPE_SINGLE;
        $command->duration = 90;
        $command->startDate = new DateTime("-2 day");
        $command->endDate = new DateTime("-1 day");
        $command->title = "New title";
        $command->method = 'single';
        $command->repetitionDays = [];
        $command->occurrence = $occurrenceMock;
        $command->calendar = $calendarMock;

        $eventMock->shouldReceive("updateWithCommand")->with($command);

        $single = new Single();
        $single->setEventRepository($eventRepositoryMock);
        $single->setOccurrenceRepository($occurrenceRepositoryMock);
        $single->update($command);
    }

    /**
     * @test
     */
    public function testUpdateWeeklyToWeekly()
    {
        $command = new UpdateEventCommand();
        $command->type = EventType::TYPE_WEEKLY;
        $command->duration = 90;
        $command->startDate = new DateTime("-2 day");
        $command->endDate = new DateTime("-1 day");
        $command->title = "New title";
        $command->method = 'single';
        $command->repetitionDays = [];

        $calendarMock = m::mock(Calendar::class);

        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("isType")->once()->with(EventType::TYPE_SINGLE)->andReturn(false);
        $eventMock->shouldReceive("isType")->once()->with(EventType::TYPE_WEEKLY)->andReturn(true);

        /** @var OccurrenceDuration|null $occurrenceDuration */
        $occurrenceDuration = null;

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive("event")->andReturn($eventMock);
        $occurrenceMock->shouldReceive("changeStartDate")->once()->with($command->startDate);
        $occurrenceMock->shouldReceive("changeDuration")->once()->andReturnUsing(function(OccurrenceDuration $duration) use (&$occurrenceDuration){
            $occurrenceDuration = $duration;
        });

        $command->occurrence = $occurrenceMock;
        $command->calendar = $calendarMock;

        $single = new Single();
        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("update")->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("update")->with($occurrenceMock);

        $single->setEventRepository($eventRepositoryMock);
        $single->setOccurrenceRepository($occurrenceRepositoryMock);
        $single->update($command);

        $this->assertEquals($command->duration, $occurrenceDuration->minutes());
    }

    /**
     * @test
     */
    public function testUpdateSingleToWeekly() {
        $newOccurrencesCollection = new ArrayCollection([]);

        $eventMock = m::mock(Event::class);

        $oldOccurrenceMock = m::mock(Occurrence::class);
        $oldOccurrenceMock->shouldReceive("event")->once()->andReturn($eventMock);
        $oldOccurrenceMock->shouldReceive('synchronizeWithEvent')->once();

        createsCommand: {
            $command = new UpdateEventCommand();
            $command->type = EventType::TYPE_WEEKLY;
            $command->duration = 60;
            $command->startDate = new DateTime("yesterday");
            $command->endDate = new DateTime("tomorrow");
            $command->title = "New title";
            $command->method = 'single';
            $command->repetitionDays = [];
            $command->occurrence = $oldOccurrenceMock;
        }

        $eventMock->shouldReceive("isType")->with('single')->once()->andReturn(true);
        $eventMock->shouldReceive("updateWithCommand")->with($command)->once();
        $eventMock->shouldReceive("setOccurrences")->with($newOccurrencesCollection)->once();

        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);
        $occurrenceFactoryMock->shouldReceive('generateCollectionFromEvent')->once()->with($eventMock)->andReturn($newOccurrencesCollection);

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("update")->once()->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("insert")->once()->with($newOccurrencesCollection);

        $single = new Single();
        $single->setOccurrenceFactory($occurrenceFactoryMock);
        $single->setOccurrenceRepository($occurrenceRepositoryMock);
        $single->setEventRepository($eventRepositoryMock);
        $single->update($command);

        $this->assertEquals($oldOccurrenceMock, $newOccurrencesCollection->first());
    }

    /**
     * @test
     */
    public function testUpdateWeeklyToSingle() {
        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("isType")->with('single')->once()->andReturn(false);
        $eventMock->shouldReceive("isType")->with('weekly')->once()->andReturn(true);

        $startDate = new DateTime("yesterday");
        $endDate = new DateTime("tomorrow");

        $newEventOccurrences = null;

        $newEventMock = m::mock(Event::class);
        $newEventMock->shouldReceive('setOccurrences')->once()->andReturnUsing(function(ArrayCollection $arrayCollection) use (&$newEventOccurrences){
            $newEventOccurrences = $arrayCollection;
        });

        $oldOccurrenceMock = m::mock(Occurrence::class);
        $oldOccurrenceMock->shouldReceive("event")->once()->andReturn($eventMock);
        $oldOccurrenceMock->shouldReceive("startDate")->once()->andReturn($startDate);
        $oldOccurrenceMock->shouldReceive("endDate")->once()->andReturn($endDate);
        $oldOccurrenceMock->shouldReceive("moveToEvent")->once()->with($newEventMock);

        createsCommand: {
            $command = new UpdateEventCommand();
            $command->type = EventType::TYPE_SINGLE;
            $command->duration = 60;
            $command->startDate = new Datetime();
            $command->endDate = new Datetime("+60 minutes");
            $command->title = "New title";
            $command->method = 'single';
            $command->repetitionDays = [];
            $command->occurrence = $oldOccurrenceMock;
        }

        $eventFactoryMock = m::mock(EventFactoryInterface::class);
        $eventFactoryMock->shouldReceive('createFromCommand')->once()->andReturn($newEventMock);

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive('insert')->once()->with($newEventMock);
        $eventRepositoryMock->shouldReceive('update')->once()->with($eventMock);

        $newEventOccurrencesToUpdate = null;

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive('update')->once()->andReturnUsing(function(ArrayCollection $arrayCollection) use (&$newEventOccurrencesToUpdate){
            $newEventOccurrencesToUpdate = $arrayCollection;
        });

        $single = new Single();
        $single->setEventFactory($eventFactoryMock);
        $single->setEventRepository($eventRepositoryMock);
        $single->setOccurrenceRepository($occurrenceRepositoryMock);
        $single->update($command);

        $this->assertSame($command->startDate, $startDate);
        $this->assertSame($command->endDate, $endDate);
        $this->assertSame($oldOccurrenceMock, $newEventOccurrences->first());
        $this->assertSame($newEventOccurrences, $newEventOccurrencesToUpdate);
    }

    /**
     * @test
     */
    public function testRemoveSingleFromSingle() {
        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("isType")->with('single')->once()->andReturn(true);

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive('event')->once()->andReturn($eventMock);

        createsCommand: {
            $command = new RemoveEventCommand();
            $command->method = 'single';
            $command->occurrence = $occurrenceMock;
        }

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive('remove')->once()->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive('remove')->once()->with($occurrenceMock);

        $single = new Single();
        $single->setEventRepository($eventRepositoryMock);
        $single->setOccurrenceRepository($occurrenceRepositoryMock);
        $single->update($command);
    }

    /**
     * @test
     */
    public function testRemoveSingleFromWeekly() {
        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("isType")->with('single')->once()->andReturn(false);

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive('event')->once()->andReturn($eventMock);

        createsCommand: {
            $command = new RemoveEventCommand();
            $command->method = 'single';
            $command->occurrence = $occurrenceMock;
        }

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive('remove')->once()->with($occurrenceMock);

        $single = new Single();
        $single->setOccurrenceRepository($occurrenceRepositoryMock);
        $single->update($command);
    }

    public function tearDown()
    {
        m::close();
    }
}
