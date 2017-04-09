<?php
namespace Dende\Calendar\Tests\Application\Handler;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Handler\UpdateEventHandler;
use Dende\Calendar\Application\Handler\UpdateStrategy\Single;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit_Framework_TestCase;

/**
 * Class EventTest.
 */
final class SingleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function test_updating_single_event()
    {
        $collection = new ArrayCollection();

        $event = new Event(
            EventId::create(),
            Calendar::create('test'),
            EventType::single(),
            new DateTime('11:00'),
            new DateTime('12:00'),
            'some Title',
            new Repetitions(),
            $collection
        );

        $occurrence = new Occurrence(
            OccurrenceId::create(), $event, new DateTime('+1 hour'), new OccurrenceDuration(60)
        );

        $collection->add($occurrence);

        $command = UpdateEventCommand::fromArray(
            [
                'method'       => UpdateEventHandler::MODE_SINGLE,
                'startDate'    => new DateTime('12:00'),
                'endDate'      => new DateTime('14:00'),
                'title'        => 'some new title',
                'repetitions'  => [],
                'occurrenceId' => $occurrence->id()->__toString(),
            ]
        );

        $eventRepository = new InMemoryEventRepository();
        $eventRepository->insert($event);

        $occurrenceRepository = new InMemoryOccurrenceRepository();
        $occurrenceRepository->insert($occurrence);

        $singleStrategy = new Single();
        $singleStrategy->setOccurrenceRepository($occurrenceRepository);
        $singleStrategy->setEventRepository($eventRepository);

        $singleStrategy->update($command);

        $this->assertEquals('some new title', $event->title());
        $this->assertEquals(new DateTime('12:00'), $event->startDate());
        $this->assertEquals(new DateTime('14:00'), $event->endDate());
        $this->assertEquals(120, $event->duration()->minutes());
        $this->assertCount(1, $event->occurrences());
        $this->assertEquals(new DateTime('12:00'), $occurrence->startDate());
        $this->assertEquals(120, $occurrence->duration()->minutes());
    }

    /**
     * @test
     */
    public function test_updating_weekly_event()
    {
        $base = Carbon::instance(new DateTime("last monday 12:00"));

        $event = new Event(
            EventId::create(),
            Calendar::create('test'),
            EventType::weekly(),
            $base->copy(),
            $base->copy()->addDays(6)->addHours(2),
            'some Title',
            Repetitions::workingDays()
        );

        $occurrence = $event->occurrences()->get(2);

        $command = UpdateEventCommand::fromArray(
            [
                'method'       => UpdateEventHandler::MODE_SINGLE,
                'startDate'    => new DateTime('12:00'),
                'endDate'      => new DateTime('14:00'),
                'title'        => 'some new title',
                'repetitions'  => [],
                'occurrenceId' => $occurrence->id()->__toString(),
            ]
        );

        $eventRepository = new InMemoryEventRepository();
        $eventRepository->insert($event);

        $occurrenceRepository = new InMemoryOccurrenceRepository();
        $occurrenceRepository->insert($occurrence);

        $singleStrategy = new Single();
        $singleStrategy->setOccurrenceRepository($occurrenceRepository);
        $singleStrategy->setEventRepository($eventRepository);

        $singleStrategy->update($command);
    }

    public function remove_occurrence_from_event()
    {
    }
}
