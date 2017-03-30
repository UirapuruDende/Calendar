<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Handler\UpdateStrategy\Single;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration as OccurrenceDuration;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Mockery as m;

/**
 * Class EventTest.
 */
class SingleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function testUpdateSingleToSingle()
    {
        $calendar = new Calendar(null, 'title');
        $calendar->createEvent('testEvent', EventType::single(), new DateTime('now'), new Datetime('+1 hour'));

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive('event')->once()->andReturn($eventMock);
        $occurrenceMock->shouldReceive('synchronizeWithEvent')->once();

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive('update')->once()->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive('update')->once()->with($occurrenceMock);

        $command              = new UpdateEventCommand();
        $command->type        = EventType::TYPE_SINGLE;
        $command->duration    = 90;
        $command->startDate   = new DateTime('-2 day');
        $command->endDate     = new DateTime('-1 day');
        $command->title       = 'New title';
        $command->method      = 'single';
        $command->repetitions = [];
        $command->occurrence  = $occurrenceMock;
        $command->calendar    = $calendarMock;

        $eventMock->shouldReceive('updateWithCommand')->with($command);

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
        $command              = new UpdateEventCommand();
        $command->type        = EventType::TYPE_WEEKLY;
        $command->duration    = 90;
        $command->startDate   = new DateTime('-2 day');
        $command->endDate     = new DateTime('-1 day');
        $command->title       = 'New title';
        $command->method      = 'single';
        $command->repetitions = [];

        $calendarMock = m::mock(Calendar::class);

        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive('isSingle')->once()->andReturn(false);
        $eventMock->shouldReceive('isWeekly')->once()->andReturn(true);

        /** @var OccurrenceDuration|null $occurrenceDuration */
        $occurrenceDuration = null;

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive('event')->andReturn($eventMock);
        $occurrenceMock->shouldReceive('changeStartDate')->once()->with($command->startDate);
        $occurrenceMock->shouldReceive('changeDuration')->once()->andReturnUsing(function (OccurrenceDuration $duration) use (&$occurrenceDuration) {
            $occurrenceDuration = $duration;
        });

        $command->occurrence = $occurrenceMock;
        $command->calendar   = $calendarMock;

        $single              = new Single();
        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive('update')->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive('update')->with($occurrenceMock);

        $single->setEventRepository($eventRepositoryMock);
        $single->setOccurrenceRepository($occurrenceRepositoryMock);
        $single->update($command);

        $this->assertEquals($command->duration, $occurrenceDuration->minutes());
    }

    /**
     * @test
     */
    public function testRemoveSingleFromSingle()
    {
        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive('isType')->with('single')->once()->andReturn(true);

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive('event')->once()->andReturn($eventMock);

        createsCommand: {
            $command             = new RemoveEventCommand();
            $command->method     = 'single';
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
    public function testRemoveSingleFromWeekly()
    {
        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive('isType')->with('single')->once()->andReturn(false);

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive('event')->once()->andReturn($eventMock);

        createsCommand: {
            $command             = new RemoveEventCommand();
            $command->method     = 'single';
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
