<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\RemoveEventCommand;
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
    public function testUpdate()
    {
        $startDate = new DateTime("-2 day");

        $calendarMock1 = m::mock(Calendar::class);

        $newOccurrenceCollectionMock = new ArrayCollection([]);

        $oldOccurrenceMock = m::mock(Occurrence::class);
        $oldOccurrenceCollectionMock = new ArrayCollection([$oldOccurrenceMock]);

        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("occurrences")->once()->andReturn($oldOccurrenceCollectionMock);
        $eventMock->shouldReceive("setOccurrences")->once()->with($newOccurrenceCollectionMock);

        $oldOccurrenceMock->shouldReceive("event")->once()->andReturn($eventMock);

        createCommand: {
            $command = new UpdateEventCommand();
            $command->type = EventType::TYPE_SINGLE;
            $command->duration = 90;
            $command->startDate = $startDate;
            $command->endDate = new DateTime("-1 day");
            $command->title = "New title";
            $command->method = 'overwrite';
            $command->repetitionDays = [];
            $command->occurrence = $oldOccurrenceMock;
            $command->calendar = $calendarMock1;
        }

        $eventMock->shouldReceive("updateWithCommand")->once()->with($command);

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("update")->once()->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("remove")->once()->with($oldOccurrenceCollectionMock);
        $occurrenceRepositoryMock->shouldReceive("insert")->once()->with($newOccurrenceCollectionMock);

        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);
        $occurrenceFactoryMock->shouldReceive("generateCollectionFromEvent")->once()->with($eventMock)->andReturn($newOccurrenceCollectionMock);

        $overwrite = new Overwrite();
        $overwrite->setOccurrenceFactory($occurrenceFactoryMock);
        $overwrite->setEventRepository($eventRepositoryMock);
        $overwrite->setOccurrenceRepository($occurrenceRepositoryMock);
        $overwrite->update($command);
    }

    public function testRemove()
    {
        $occurrenceMock = m::mock(Occurrence::class);
        $occurrencesCollectionMock = new ArrayCollection([$occurrenceMock]);

        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("occurrences")->once()->andReturn($occurrencesCollectionMock);

        $occurrenceMock->shouldReceive("event")->once()->andReturn($eventMock);

        createCommand: {
            $command = new RemoveEventCommand();
            $command->method = 'overwrite';
            $command->occurrence = $occurrenceMock;
        }

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive("remove")->once()->with($eventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive("remove")->once()->with($occurrencesCollectionMock);

        $overwrite = new Overwrite();
        $overwrite->setEventRepository($eventRepositoryMock);
        $overwrite->setOccurrenceRepository($occurrenceRepositoryMock);
        $overwrite->update($command);
    }

    public function tearDown()
    {
        m::close();
    }
}
