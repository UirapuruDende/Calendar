<?php
namespace Dende\Calendar\Tests\Application\Handler;


use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Handler\UpdateEventHandler;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use PHPUnit_Framework_TestCase;

class UpdateEventHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_updates_event()
    {
        $base = Carbon::instance(new DateTime('last monday 12:00'));

        $eventId = EventId::create();

        $event = new Event(
            $eventId,
            Calendar::create('test'),
            EventType::weekly(),
            $base->copy(),
            $base->copy()->addDays(6)->addHours(2),
            'some Title',
            Repetitions::workingDays()
        );

        $eventRepository = new InMemoryEventRepository();
        $eventRepository->insert($event);

        $command = new UpdateEventCommand(
            $eventId->__toString(),
            $base->copy()->addDays(1),
            $base->copy()->addDays(5)->addHours(3),
            'new title',
            Repetitions::weekendDays()->getArray()
        );

        $handler = new UpdateEventHandler($eventRepository);
        $handler->handle($command);

        $this->assertEquals($event->id(), $eventId);
        $this->assertEquals($event->startDate(), $base->copy()->addDays(1));
        $this->assertEquals($event->endDate(), $base->copy()->addDays(5)->addHours(3));
        $this->assertEquals($event->duration()->minutes(), 180);
    }
}
