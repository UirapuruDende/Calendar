<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\UpdateStrategy\NextInclusive;
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

final class NextInclusiveTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $pivotDate = new DateTime("now");
        $newOccurrencesCollection = $this->getNewEventOccurrencesCollection();
        $oldOccurrencesCollection = $this->getOldEventOccurrencesCollection();

        $eventMock = m::mock(Event::class);
        $eventMock->shouldReceive("changeEndDate")->with($pivotDate);
        $eventMock->shouldReceive("occurrences")->andReturn($oldOccurrencesCollection);

        $filteredOccurrences = null;

        $eventMock->shouldReceive("setOccurrences")->andReturnUsing(
            function(ArrayCollection $collection) use (&$filteredOccurrences) {
                $filteredOccurrences = $collection;
            }
        );

        $newEventMock = m::mock(Event::class);
        $newEventMock->shouldReceive("setOccurrences")->with($newOccurrencesCollection);

        $occurrenceMock = m::mock(Occurrence::class);
        $occurrenceMock->shouldReceive("event")->andReturn($eventMock);
        $occurrenceMock->shouldReceive("startDate")->andReturn($pivotDate);

        $command = new UpdateEventCommand();
        $command->type = EventType::TYPE_SINGLE;
        $command->duration = 90;
        $command->startDate = new DateTime("-2 day");
        $command->endDate = new DateTime("-1 day");
        $command->title = "New title";
        $command->method = 'next_inclusive';
        $command->repetitionDays = [];
        $command->occurrence = $occurrenceMock;

        $eventFactoryMock = m::mock(EventFactoryInterface::class);
        $eventFactoryMock->shouldReceive("createFromCommand")->with($command)->andReturn($newEventMock);

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

        $this->assertCount(1, $filteredOccurrences);
    }

    public function tearDown()
    {
        m::close();
    }

    private function getOldEventOccurrencesCollection()
    {
        $mock1 = m::mock(Occurrence::class);
        $mock1->shouldReceive("endDate")->andReturn(new Datetime("yesterday"));

        $mock2 = m::mock(Occurrence::class);
        $mock2->shouldReceive("endDate")->andReturn(new Datetime("now"));

        $mock3 = m::mock(Occurrence::class);
        $mock3->shouldReceive("endDate")->andReturn(new Datetime("tomorrow"));

        return new ArrayCollection([
            $mock1,
            $mock2,
            $mock3
        ]);
    }

    private function getNewEventOccurrencesCollection()
    {
        return new ArrayCollection([]);
    }
}
