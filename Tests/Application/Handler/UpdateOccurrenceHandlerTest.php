<?php
namespace Dende\Calendar\Tests\Application\Handler;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\UpdateOccurrenceCommand;
use Dende\Calendar\Application\Handler\UpdateOccurrenceHandler;
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
final class UpdateOccurrenceHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function test_updating_single_occurrence()
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

        $command = new UpdateOccurrenceCommand($occurrence->id()->__toString(), new DateTime('12:00'), new DateTime('14:00'));

        $eventRepository = new InMemoryEventRepository();
        $eventRepository->insert($event);

        $occurrenceRepository = new InMemoryOccurrenceRepository();
        $occurrenceRepository->insert($occurrence);

        $updateOccurrenceHandler = new UpdateOccurrenceHandler(
            $occurrenceRepository
        );

        $updateOccurrenceHandler->handle($command);

        $this->assertEquals(new DateTime('11:00'), $event->startDate());
        $this->assertEquals(new DateTime('12:00'), $event->endDate());
        $this->assertEquals(60, $event->duration()->minutes());
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

        $command = new UpdateOccurrenceCommand(
            $occurrence->id()->__toString(),
            $base->copy()->addDays(2)->addHours(2),
            $base->copy()->addDays(2)->addHours(5)
        );

        $occurrenceRepository = new InMemoryOccurrenceRepository();
        $occurrenceRepository->insert($occurrence);

        $updateOccurrenceHandler = new UpdateOccurrenceHandler(
            $occurrenceRepository
        );

        $updateOccurrenceHandler->handle($command);

        $occurrence = $occurrenceRepository->findAll()->first();

        $this->assertEquals(180, $occurrence->duration()->minutes());
        $this->assertEquals($base->copy()->addDays(2)->addHours(2), $occurrence->startDate());
    }
}
