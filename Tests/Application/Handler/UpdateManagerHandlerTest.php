<?php
namespace Dende\Calendar\Tests\Application\Handler;

use DateTime;
use Dende\Calendar\Application\Command\UpdateCommand;
use Dende\Calendar\Application\Handler\UpdateManager;
use Dende\Calendar\Application\Handler\UpdateStrategy\UpdateStrategyInterface;
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
use PHPUnit_Framework_TestCase;

/**
 * Class EventTest.
 */
final class UpdateManagerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped();
    }
    
    public function testHandleUpdateCommand()
    {
        $event      = new Event(EventId::create(), Calendar::create('test'), EventType::single(), new DateTime('12:00'), new DateTime('13:00'), 'some Title', new Repetitions());
        $occurrence = new Occurrence(OccurrenceId::create(), $event, new DateTime('+1 hour'), new OccurrenceDuration(60));

        $command = UpdateCommand::fromArray([
             'occurrenceId' => $occurrence->id()->__toString(),
             'method'       => UpdateManager::MODE_SINGLE,
            'startDate'     => new DateTime('12:00'),
            'endDate'       => new DateTime('14:00'),
            'title'         => $event->title(),
            'repetitions'   => [],
        ]);

        $eventRepository      = new InMemoryEventRepository();
        $occurrenceRepository = new InMemoryOccurrenceRepository();

        $strategyMock = $this->prophesize(UpdateStrategyInterface::class);
        $strategyMock->update($command)->shouldBeCalled()->willReturn(null);
        $strategyMock->setEventRepository($eventRepository)->shouldBeCalled()->willReturn(null);
        $strategyMock->setOccurrenceRepository($occurrenceRepository)->shouldBeCalled()->willReturn(null);

        $handler = new UpdateManager($eventRepository, $occurrenceRepository);
        $handler->addStrategy(UpdateManager::MODE_SINGLE, $strategyMock->reveal());

        $handler->handle($command);
    }

    /**
     * @throws \Exception
     * @expectedException \Exception
     * @expectedExceptionMessage Mode 'single' not allowed. Only  allowed.
     */
    public function testStrategyNotSetException()
    {
        $command = new UpdateCommand(
            '',
            UpdateManager::MODE_SINGLE,
            new DateTime('12:00'),
            new DateTime('13:00'),
            '',
            []
        );

        $handler = new UpdateManager(new InMemoryEventRepository(), new InMemoryOccurrenceRepository());

        $handler->handle($command);
    }
}
