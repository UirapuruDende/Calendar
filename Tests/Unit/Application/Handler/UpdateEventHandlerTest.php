<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler;

use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\UpdateEventHandler;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
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
use Dende\Calendar\Application\Handler\UpdateStrategy\UpdateStrategyInterface;
use Mockery as m;
use PHPUnit_Framework_TestCase;

/**
 * Class EventTest.
 */
final class UpdateEventHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testHandleUpdateCommand()
    {
        $this->markTestIncomplete();
        $event      = new Event(EventId::create(), Calendar::create('test'), EventType::single(), new DateTime('12:00'), new DateTime('13:00'), 'some Title', new Repetitions());
        $occurrence = new Occurrence(OccurrenceId::create(), $event, new DateTime('+1 hour'), new OccurrenceDuration(60));

        $command = UpdateEventCommand::fromArray([
            'method'       => UpdateEventHandler::MODE_SINGLE,
            'startDate'    => new DateTime('12:00'),
            'endDate'      => new DateTime('14:00'),
            'title'        => $event->title(),
            'repetitions'  => [],
            'occurrenceId' => $occurrence->id()->__toString(),
        ]);

        $eventRepository      = new InMemoryEventRepository();
        $occurrenceRepository = new InMemoryOccurrenceRepository();

        $strategyMock = $this->prophesize(UpdateStrategyInterface::class);
        $strategyMock->update($command)->shouldBeCalled()->willReturn(null);
        $strategyMock->setEventRepository($eventRepository)->shouldBeCalled()->willReturn(null);
        $strategyMock->setOccurrenceRepository($occurrenceRepository)->shouldBeCalled()->willReturn(null);

        $handler = new UpdateEventHandler($eventRepository, $occurrenceRepository);
        $handler->addStrategy(UpdateEventHandler::MODE_SINGLE, $strategyMock->reveal());

        $handler->handle($command);
    }

    /**
     * @throws \Exception
     * @expectedException \Exception
     * @expectedExceptionMessage Mode 'single' not allowed. Only  allowed.
     */
    public function testStrategyNotSetException()
    {
        $command            = new UpdateEventCommand();
        $command->method    = UpdateEventHandler::MODE_SINGLE;
        $command->startDate = new DateTime('12:00');
        $command->endDate   = new DateTime('13:00');

        $handler = new UpdateEventHandler(new InMemoryEventRepository(), new InMemoryOccurrenceRepository());

        $handler->handle($command);
    }

    public function tearDown()
    {
        m::close();
    }
}
