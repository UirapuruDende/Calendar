<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Handler\UpdateStrategy\Single;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Mockery as m;

/**
 * Class EventTest
 * @package Gyman\Domain\Tests\Unit\Model
 */
final class SingleTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateSingleType()
    {
        $command = new UpdateEventCommand();
        $command->type = EventType::TYPE_SINGLE;
        $command->duration = 90;
        $command->startDate = new DateTime("-2 day");
        $command->endDate = new DateTime("-1 day");
        $command->title = "New title";
        $command->method = 'single';
        $command->repetitionDays = [];

        $calendarMock = m::mock(Calendar::class);

        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("isType")->with($command->type)->andReturn(true);
        $eventMock->shouldReceive("isType")->with(EventType::TYPE_WEEKLY)->andReturn(false);
        $eventMock->shouldReceive("changeStartDate")->with($command->startDate);
        $eventMock->shouldReceive("changeEndDate")->with($command->endDate);
        $eventMock->shouldReceive("changeDuration");
        $eventMock->shouldReceive("changeTitle")->with($command->title);

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive("event")->andReturn($eventMock);
        $occurrenceMock->shouldReceive("changeStartDate")->with($command->startDate);
        $occurrenceMock->shouldReceive("changeDuration");

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
    }

    public function testUpdateWeeklyType()
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
        $eventMock->shouldReceive("isType")->with(EventType::TYPE_SINGLE)->andReturn(false);
        $eventMock->shouldReceive("isType")->with(EventType::TYPE_WEEKLY)->andReturn(true);
        $eventMock->shouldReceive("changeStartDate")->with($command->startDate);
        $eventMock->shouldReceive("changeEndDate")->with($command->endDate);
        $eventMock->shouldReceive("changeDuration");
        $eventMock->shouldReceive("changeTitle")->with($command->title);

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive("event")->andReturn($eventMock);
        $occurrenceMock->shouldReceive("changeStartDate")->with($command->startDate);
        $occurrenceMock->shouldReceive("changeDuration");

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
    }

    public function tearDown()
    {
        m::close();
    }
}
