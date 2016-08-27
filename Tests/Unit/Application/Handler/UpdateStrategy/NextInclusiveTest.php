<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\UpdateStrategy\NextInclusive;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class NextInclusiveTest extends PHPUnit_Framework_TestCase
{
    public function testUpdateSingle() {
        $pivotDate = new DateTime("now");
        $newOccurrencesCollection = new ArrayCollection([]);

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

        $nextInclusive = new NextInclusive();
        $nextInclusive->setEventFactory($eventFactoryMock);
        $nextInclusive->setOccurrenceFactory($occurrenceFactoryMock);
        $nextInclusive->setEventRepository($eventRepositoryMock);
        $nextInclusive->update($command);
    }

    public function testUpdateWeekly()
    {
        $pivotDate = new DateTime("now");
        $newOccurrencesCollection = new ArrayCollection([]);

        $eventMock = m::mock(Event::class);

        occurrencesCollection: {
            $oldOccurrenceMock1 = m::mock(Occurrence::class);
            $oldOccurrenceMock1->shouldReceive("endDate")->andReturn(new Datetime("yesterday +90 minutes"));

            $oldOccurrenceMock2 = m::mock(Occurrence::class);
            $oldOccurrenceMock2->shouldReceive("startDate")->andReturn($pivotDate);
            $oldOccurrenceMock2->shouldReceive("endDate")->andReturn(new Datetime("+90 minutes"));
            $oldOccurrenceMock2->shouldReceive("event")->andReturn($eventMock);
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

        $eventMock->shouldReceive("changeEndDate")->with($pivotDate);
        $eventMock->shouldReceive("occurrences")->andReturn($oldOccurrencesCollection);
        $eventMock->shouldReceive("type->isType")->with('single')->andReturn(false);
        $eventMock->shouldReceive("type->isType")->with('weekly')->andReturn(true);

        $filteredOccurrences = null;

        $eventMock->shouldReceive("setOccurrences")->andReturnUsing(
            function(ArrayCollection $collection) use (&$filteredOccurrences) {
                $filteredOccurrences = $collection;
            }
        );

        $newEventMock = m::mock(Event::class);
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
        $eventRepositoryMock->shouldReceive("update")->with($eventMock);
        $eventRepositoryMock->shouldReceive("insert")->with($newEventMock);

        $nextInclusive = new NextInclusive();
        $nextInclusive->setEventFactory($eventFactoryMock);
        $nextInclusive->setOccurrenceFactory($occurrenceFactoryMock);
        $nextInclusive->setEventRepository($eventRepositoryMock);
        $nextInclusive->update($command);
    }

    public function tearDown()
    {
        m::close();
    }

}
