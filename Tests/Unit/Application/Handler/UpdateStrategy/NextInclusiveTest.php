<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use DateTime;
use Dende\Calendar\Application\Command\RemoveEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\UpdateStrategy\NextInclusive;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
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
            $occurrence1 = new Occurrence(null, new DateTime('2016-09-01 12:00:00'), new OccurrenceDuration(60), $eventMock);
            $occurrence2 = new Occurrence(null, new DateTime('2016-09-02 12:00:00'), new OccurrenceDuration(60), $eventMock);
            $occurrence3 = new Occurrence(null, new DateTime('2016-09-03 12:00:00'), new OccurrenceDuration(60), $eventMock);
            $occurrence4 = new Occurrence(null, new DateTime('2016-09-04 12:00:00'), new OccurrenceDuration(60), $eventMock);
            $occurrence5 = new Occurrence(null, new DateTime('2016-09-05 12:00:00'), new OccurrenceDuration(60), $eventMock);
        }

        $occurrences = [
            $occurrence5,
            $occurrence2,
            $occurrence1,
            $occurrence4,
            $occurrence3,
        ];

        $occurrencesCollectionMock = new ArrayCollection($occurrences);

        $eventMock->shouldReceive('occurrences')->times(5)->andReturn($occurrencesCollectionMock);

        $nextInclusive = new NextInclusive();

        $this->assertEquals($nextInclusive->findPivotDate($occurrence5, $eventMock), $occurrence4->endDate());
        $this->assertEquals($nextInclusive->findPivotDate($occurrence4, $eventMock), $occurrence3->endDate());
        $this->assertEquals($nextInclusive->findPivotDate($occurrence3, $eventMock), $occurrence2->endDate());
        $this->assertEquals($nextInclusive->findPivotDate($occurrence2, $eventMock), $occurrence1->endDate());
        $this->assertEquals($nextInclusive->findPivotDate($occurrence1, $eventMock), $occurrence1->endDate());
    }

    public function testUpdate()
    {
        $pivotDate = new DateTime('now');
        $newOccurrencesCollection = new ArrayCollection([]);

        $deletedDate1 = null;
        $deletedDate2 = null;
        $deletedDate3 = null;

        /** @var m\MockInterface|Event $originalEventMock */
        $originalEventMock = m::mock(Event::class);

        occurrencesCollection: {

            $oldOccurrence1 = new Occurrence(null, new DateTime('yesterday'), new OccurrenceDuration(90));
            $oldOccurrence2 = new Occurrence(null, new DateTime('today'), new OccurrenceDuration(90));
            $oldOccurrence3 = new Occurrence(null, new DateTime('tomorrow'), new OccurrenceDuration(90));

            $oldOccurrencesCollection = new ArrayCollection([
                $oldOccurrence1,
                $oldOccurrence2,
                $oldOccurrence3,
            ]);
        }

        $newEventMock = m::mock(Event::class);

        $originalEventMock->shouldReceive('occurrences')->times(3)->andReturn($oldOccurrencesCollection);
        $originalEventMock->shouldReceive('isSingle')->once()->andReturn(false);
        $originalEventMock->shouldReceive('isWeekly')->once()->andReturn(true);
        $originalEventMock->shouldReceive('calendar')->once()->andReturn(m::mock(Calendar::class));
        $originalEventMock->shouldReceive('type->type')->once()->andReturn('weekly');
        $originalEventMock->shouldReceive('closeAtDate')->once();

        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);
        $newEventMock->shouldReceive('generateOccurrencesCollection')->once()->with($occurrenceFactoryMock);

        createsCommand: {
            $command = new UpdateEventCommand();
            $command->startDate = new DateTime('yesterday');
            $command->endDate = new DateTime('tomorrow');
            $command->title = 'New title';
            $command->method = 'nextinclusive';
            $command->repetitionDays = [];
            $command->occurrence = $oldOccurrence2;
        }

        $eventFactoryMock = m::mock(EventFactoryInterface::class);
        $eventFactoryMock->shouldReceive('createFromCommand')->andReturn($newEventMock);

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $eventRepositoryMock->shouldReceive('update')->once()->with($originalEventMock);
        $eventRepositoryMock->shouldReceive('insert')->once()->with($newEventMock);
        $eventRepositoryMock->shouldReceive('findOneByOccurrence')->once()->with($oldOccurrence2)->andReturn($originalEventMock);

        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $occurrenceRepositoryMock->shouldReceive('update')->once()->with($oldOccurrencesCollection);

        $nextInclusive = new NextInclusive();
        $nextInclusive->setEventFactory($eventFactoryMock);
        $nextInclusive->setOccurrenceRepository($occurrenceRepositoryMock);
        $nextInclusive->setOccurrenceFactory($occurrenceFactoryMock);
        $nextInclusive->setEventRepository($eventRepositoryMock);
        $nextInclusive->update($command);

        $this->assertEquals(new DateTime(), $deletedDate1);
        $this->assertEquals(new DateTime(), $deletedDate2);
        $this->assertEquals(new DateTime(), $deletedDate3);
    }

    public function testRemove()
    {
        $pivotDate = new DateTime('now');

        /** @var m\MockInterface|Event $originalEventMock */

        occurrencesCollection: {
            $occurrence1 = new Occurrence(null, new DateTime('-1 day'), new OccurrenceDuration(90));
            $occurrence2 = new Occurrence(null, new DateTime('-2 hours'), new OccurrenceDuration(90));
            $occurrence3 = new Occurrence(null, new DateTime('+1 day'), new OccurrenceDuration(90));

            $oldOccurrencesCollection = new ArrayCollection([$occurrence1, $occurrence2, $occurrence3]);
        }

        $originalEventMock = new Event(0, Event\EventType::createWeekly(), new DateTime('yesterday'), new DateTime('tomorrow'), 'title', Event\Repetitions::workingDays(), $oldOccurrencesCollection);

        createsCommand: {
            $command = new RemoveEventCommand();
            $command->method = 'nextinclusive';
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
