<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Handler\UpdateEventHandler;
use Exception;
use Mockery as m;

/**
 * Class EventTest
 * @package Gyman\Domain\Tests\Unit\Model
 */
final class UpdateEventHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleUpdateCommand()
    {
        $command = new UpdateEventCommand();
        $command->method = UpdateEventHandler::MODE_SINGLE;

        $eventRepositoryMock = m::mock("Dende\Calendar\Domain\Repository\EventRepositoryInterface");
        $occurrenceRepositoryMock = m::mock("Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface");
        $strategyMock = m::mock("Dende\Calendar\Application\Handler\UpdateStrategy\UpdateStrategyInterface");
        $strategyMock->shouldReceive('update')->once()->with($command);
        $strategyMock->shouldReceive('setEventRepository')->once()->with($eventRepositoryMock);
        $strategyMock->shouldReceive('setOccurrenceRepository')->once()->with($occurrenceRepositoryMock);

        $handler = new UpdateEventHandler($eventRepositoryMock, $occurrenceRepositoryMock);
        $handler->addStrategy(UpdateEventHandler::MODE_SINGLE, $strategyMock);

        $handler->handle($command);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     * @expectedExceptionMessage Mode 'weird_mode' not allowed. Only single, all_inclusive, all_exclusive, next_inclusive, next_exclusive, overwrite allowed.
     */
    public function testMethodNotAllowedException()
    {
        $command = new UpdateEventCommand();
        $command->method = 'weird_mode';

        $eventRepositoryMock = m::mock("Dende\Calendar\Domain\Repository\EventRepositoryInterface");
        $occurrenceRepositoryMock = m::mock("Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface");

        $handler = new UpdateEventHandler($eventRepositoryMock, $occurrenceRepositoryMock);

        $handler->handle($command);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     * @expectedExceptionMessage Strategy 'single' has not been added. Use UpdateEventHandler::addStrategy() method to add it
     */
    public function testStrategyNotSetException()
    {
        $command = new UpdateEventCommand();
        $command->method = UpdateEventHandler::MODE_SINGLE;

        $eventRepositoryMock = m::mock("Dende\Calendar\Domain\Repository\EventRepositoryInterface");
        $occurrenceRepositoryMock = m::mock("Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface");

        $handler = new UpdateEventHandler($eventRepositoryMock, $occurrenceRepositoryMock);

        $handler->handle($command);
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Strategy 'weird_strategy' not allowed. Only single, all_inclusive, all_exclusive, next_inclusive, next_exclusive, overwrite allowed.
     */
    public function testStrategyNotAllowedException()
    {
        $command = new UpdateEventCommand();
        $command->method = UpdateEventHandler::MODE_SINGLE;

        $eventRepositoryMock = m::mock("Dende\Calendar\Domain\Repository\EventRepositoryInterface");
        $occurrenceRepositoryMock = m::mock("Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface");
        $strategyMock = m::mock("Dende\Calendar\Application\Handler\UpdateStrategy\UpdateStrategyInterface");

        $handler = new UpdateEventHandler($eventRepositoryMock, $occurrenceRepositoryMock);
        $handler->addStrategy('weird_strategy', $strategyMock);

        $handler->handle($command);
    }

    public function tearDown()
    {
        m::close();
    }
}
