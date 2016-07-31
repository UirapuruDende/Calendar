<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\UpdateStrategy\Overwrite;
use Dende\Calendar\Application\Handler\UpdateStrategy\Single;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

final class OverwriteTest extends \PHPUnit_Framework_TestCase
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

        $calendarMock1 = m::mock(Calendar::class);
        $calendarMock1->shouldReceive("id")->andReturn(123);

        $calendarMock2 = m::mock(Calendar::class);
        $calendarMock2->shouldReceive("id")->andReturn(125);

        $occurrenceCollectionMock = new ArrayCollection([]);

        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("isType")->with($command->type)->andReturn(true);
        $eventMock->shouldReceive("isType")->with(EventType::TYPE_WEEKLY)->andReturn(false);
        $eventMock->shouldReceive("changeStartDate")->with($command->startDate);
        $eventMock->shouldReceive("changeEndDate")->with($command->endDate);
        $eventMock->shouldReceive("changeTitle")->with($command->title);
        $eventMock->shouldReceive("changeDuration");
        $eventMock->shouldReceive("changeCalendar")->with($calendarMock1);
        $eventMock->shouldReceive("changeType");
        $eventMock->shouldReceive("changeRepetitions");
        $eventMock->shouldReceive("calendar")->andReturn($calendarMock2);
        $eventMock->shouldReceive("setOccurrences")->with($occurrenceCollectionMock);

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive("event")->andReturn($eventMock);
        $occurrenceMock->shouldReceive("changeStartDate")->with($command->startDate);
        $occurrenceMock->shouldReceive("changeDuration");

        $command->occurrence = $occurrenceMock;
        $command->calendar = $calendarMock1;

        $overwrite = new Overwrite();
        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("update")->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("removeAllForEvent")->with($eventMock);

        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);
        $occurrenceFactoryMock->shouldReceive("generateCollectionFromEvent")->andReturn($occurrenceCollectionMock);

        $overwrite->setOccurrenceFactory($occurrenceFactoryMock);
        $overwrite->setEventRepository($eventRepositoryMock);
        $overwrite->setOccurrenceRepository($occurrenceRepositoryMock);
        $overwrite->update($command);
    }

    public function tearDown()
    {
        m::close();
    }
}
