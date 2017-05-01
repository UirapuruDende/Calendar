<?php
namespace Dende\Calendar\Tests\Application\Handler;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Events;
use Dende\Calendar\Application\Handler\UpdateEventHandler;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

        /** @var OccurrenceInterface $occurrence */
        $occurrence = $event->occurrences()->get(2);

        $command = new UpdateEventCommand(
            $eventId->__toString(),
            $base->copy()->addDays(1),
            $base->copy()->addDays(5)->addHours(3),
            'new title',
            Repetitions::weekendDays()->getArray(),
            $occurrence->id()->__toString(),
            'single'
        );

        /** @var ObjectProphecy $dispatcherMock */
        $dispatcherMock = $this->prophesize(EventDispatcherInterface::class);
        $dispatcherMock->dispatch(Events::POST_UPDATE_EVENT, Argument::type('object'))->shouldBeCalled();

        $handler = new UpdateEventHandler($eventRepository, $dispatcherMock->reveal());
        $handler->handle($command);

        $this->assertEquals($event->id(), $eventId);
        $this->assertEquals($event->startDate(), $base->copy()->addDays(1));
        $this->assertEquals($event->endDate(), $base->copy()->addDays(5)->addHours(3));
        $this->assertEquals($event->duration()->minutes(), 180);
    }
}
