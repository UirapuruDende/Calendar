<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler;

use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\UpdateEventHandler;
use Dende\Calendar\Application\Handler\UpdateStrategy\UpdateStrategyInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Exception;
use Mockery as m;

/**
 * Class EventTest.
 */
final class UpdateEventHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleUpdateCommand()
    {
        $calendar = new Calendar('test');
        $event = new Event(null, EventType::createSingle(), new DateTime('+1 hour'), new DateTime('+2 hour'), 'some Title', new Repetitions());
        $occurrence = new Occurrence(null, new DateTime('+1 hour'), new OccurrenceDuration(60), $event);

        $command = UpdateEventCommand::fromArray([
            'method'     => UpdateEventHandler::MODE_SINGLE,
            'startDate'  => new DateTime('+1 hour'),
            'endDate'    => new DateTime('+3 hour'),
            'occurrence' => $occurrence,
        ]);

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $eventFactoryMock = m::mock(EventFactoryInterface::class);
        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);

        /** @var UpdateStrategyInterface|m\Mock $strategyMock */
        $strategyMock = m::mock(UpdateStrategyInterface::class);
        $strategyMock->shouldReceive('update')->once()->with($command);
        $strategyMock->shouldReceive('setEventRepository')->once()->with($eventRepositoryMock);
        $strategyMock->shouldReceive('setOccurrenceRepository')->once()->with($occurrenceRepositoryMock);
        $strategyMock->shouldReceive('setEventFactory')->once()->with($eventFactoryMock);
        $strategyMock->shouldReceive('setOccurrenceFactory')->once()->with($occurrenceFactoryMock);

        $handler = new UpdateEventHandler($eventRepositoryMock, $occurrenceRepositoryMock, $eventFactoryMock, $occurrenceFactoryMock);
        $handler->addStrategy(UpdateEventHandler::MODE_SINGLE, $strategyMock);

        $handler->handle($command);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Strategy name 'single' already set!
     */
    public function testStrategyAlreadySet()
    {
        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $eventFactoryMock = m::mock(EventFactoryInterface::class);
        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);

        /** @var UpdateStrategyInterface|m\Mock $strategyMock */
        $strategyMock = m::mock(UpdateStrategyInterface::class);

        $handler = new UpdateEventHandler($eventRepositoryMock, $occurrenceRepositoryMock, $eventFactoryMock, $occurrenceFactoryMock);
        $strategyMock->shouldReceive('setEventRepository')->once()->with($eventRepositoryMock);
        $strategyMock->shouldReceive('setOccurrenceRepository')->once()->with($occurrenceRepositoryMock);
        $strategyMock->shouldReceive('setEventFactory')->once()->with($eventFactoryMock);
        $strategyMock->shouldReceive('setOccurrenceFactory')->once()->with($occurrenceFactoryMock);

        $handler->addStrategy(UpdateEventHandler::MODE_SINGLE, $strategyMock);
        $handler->addStrategy(UpdateEventHandler::MODE_SINGLE, $strategyMock);
    }

    /**
     * @throws \Exception
     * @expectedException \Exception
     * @expectedExceptionMessage Mode 'weird_mode' not allowed. Only single, nextinclusive, overwrite allowed.
     */
    public function testMethodNotAllowedException()
    {
        $command = new UpdateEventCommand();
        $command->method = 'weird_mode';
        $command->calendar = m::mock(Calendar::class);
        $command->startDate = new DateTime('+1 hour');
        $command->endDate = new DateTime('+2 hour');

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $eventFactoryMock = m::mock(EventFactoryInterface::class);
        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);

        $handler = new UpdateEventHandler($eventRepositoryMock, $occurrenceRepositoryMock, $eventFactoryMock, $occurrenceFactoryMock);

        $handler->handle($command);
    }

    /**
     * @throws \Exception
     * @expectedException \Exception
     * @expectedExceptionMessage Strategy 'single' has not been added. Use UpdateEventHandler::addStrategy() method to add it
     */
    public function testStrategyNotSetException()
    {
        $command = new UpdateEventCommand();
        $command->method = UpdateEventHandler::MODE_SINGLE;
        $command->startDate = new DateTime('+1 hour');
        $command->endDate = new DateTime('+2 hour');

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $eventFactoryMock = m::mock(EventFactoryInterface::class);
        $occurrenceFactoryMock = m::mock(OccurrenceFactoryInterface::class);

        $handler = new UpdateEventHandler($eventRepositoryMock, $occurrenceRepositoryMock, $eventFactoryMock, $occurrenceFactoryMock);

        $handler->handle($command);
    }

    /**
     * @throws Exception
     * @expectedException \Exception
     * @expectedExceptionMessage Strategy 'weird_strategy' not allowed. Only single, nextinclusive, overwrite allowed.
     */
    public function testStrategyNotAllowedException()
    {
        $command = new UpdateEventCommand();
        $command->method = UpdateEventHandler::MODE_SINGLE;

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $occurrenceRepositoryMock = m::mock(OccurrenceRepositoryInterface::class);
        $strategyMock = m::mock(UpdateStrategyInterface::class);
        $eventFactoryMock = m::mock(EventFactory::class);
        $occurrenceFactoryMock = m::mock(OccurrenceFactory::class);

        $handler = new UpdateEventHandler($eventRepositoryMock, $occurrenceRepositoryMock, $eventFactoryMock, $occurrenceFactoryMock);
        $handler->addStrategy('weird_strategy', $strategyMock);

        $handler->handle($command);
    }

    public function tearDown()
    {
        m::close();
    }
}
