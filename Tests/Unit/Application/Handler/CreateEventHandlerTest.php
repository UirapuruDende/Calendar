<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler;

use DateTime;
use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Handler\CreateEventHandler;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryCalendarRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use PHPUnit_Framework_TestCase;

class CreateEventHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_tests_creation_of_single_event()
    {
        $calendar = Calendar::create('test');

        $calendarId = $calendar->id()->__toString();

        $command = CreateEventCommand::fromArray([
            'calendarId'  => $calendarId,
            'startDate'   => new DateTime('+1 hour'),
            'endDate'     => new DateTime('+1 day +3 hours'),
            'type'        => EventType::TYPE_SINGLE,
            'title'       => 'some-title',
            'repetitions' => [],
       ]);

        $calendarRepository = new InMemoryCalendarRepository();
        $calendarRepository->insert($calendar);

        $eventRepository      = new InMemoryEventRepository();
        $occurrenceRepository = new InMemoryOccurrenceRepository();

        $handler = new CreateEventHandler($calendarRepository, $eventRepository, $occurrenceRepository);
        $handler->handle($command);

        $event = $calendar->events()->last();

        $this->assertEquals(new DateTime('+3 hours'), $event->endDate());
        $this->assertCount(1, $event->occurrences());
    }

    /**
     * @test
     */
    public function it_tests_creation_of_weekly_event()
    {
        $calendar = Calendar::create('test');

        $calendarId = $calendar->id()->__toString();

        $startDate = new DateTime('last monday');
        $endDate   = (clone $startDate)->modify('+6 days +3 hours');

        $command = CreateEventCommand::fromArray([
            'calendarId'  => $calendarId,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'type'        => EventType::TYPE_WEEKLY,
            'title'       => 'some-title',
            'repetitions' => Repetitions::workingDays()->getArray(),
       ]);

        $calendarRepository = new InMemoryCalendarRepository();
        $calendarRepository->insert($calendar);

        $eventRepository      = new InMemoryEventRepository();
        $occurrenceRepository = new InMemoryOccurrenceRepository();

        $handler = new CreateEventHandler($calendarRepository, $eventRepository, $occurrenceRepository);
        $handler->handle($command);

        $this->assertCount(1, $eventRepository->findAll());
        $this->assertCount(5, $occurrenceRepository->findAll());
        $this->assertEquals($calendar->events()->last(), $eventRepository->findAll()->last());
    }
}
