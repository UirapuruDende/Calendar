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
    public function setUp()
    {
        $this->markTestSkipped('Will be moved to update occurrence handler test');
    }

    /**
     * @test
     */
    public function test_updating_single_event()
    {
        $baseTime = Carbon::instance(new DateTime('today 11:00'));

        $collection = new ArrayCollection();

        $event = new Event(
            EventId::create(),
            Calendar::create('test'),
            EventType::single(),
            $baseTime->copy(),
            $baseTime->copy()->modify('+1 hour'),
            'some Title',
            new Repetitions(),
            $collection
        );

        $occurrence = new Occurrence(
            OccurrenceId::create(), $event, $baseTime->copy(), new OccurrenceDuration(60)
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
        $base = Carbon::instance(new DateTime('last monday 12:00'));

        $event = new Event(
            EventId::create(),
            Calendar::create('test'),
            EventType::weekly(),
            $base->copy(),
            $base->copy()->addDays(6)->addHours(2),
            'some Title',
            Repetitions::workingDays()
        );

        /** @var Occurrence $occurrence */
        $occurrence = $event->occurrences()->get(2);

        $command = UpdateEventCommand::fromArray(
            [
                'method'       => UpdateEventHandler::MODE_SINGLE,
                'startDate'    => $base->copy()->addDays(2)->addHours(2),
                'endDate'      => $base->copy()->addDays(2)->addHours(5),
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

        $occurrence = $occurrenceRepository->findAll()->first();

        $this->assertEquals(180, $occurrence->duration()->minutes());
        $this->assertEquals($base->copy()->addDays(2)->addHours(2), $occurrence->startDate());
    }

    public function remove_occurrence_from_event()
    {
    }
}
