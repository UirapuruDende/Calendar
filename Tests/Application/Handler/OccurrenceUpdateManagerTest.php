<?php
namespace Dende\Calendar\Tests\Application\Handler;

use DateTime;
use Dende\Calendar\Application\Command\UpdateOccurrenceCommand;
use Dende\Calendar\Application\Handler\OccurrenceUpdateManager;
use Dende\Calendar\Application\Service\UpdateStrategy\UpdateStrategyInterface;
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
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class OccurrenceUpdateManagerTest extends TestCase
{
    public function setUp()
    {
//        $this->markTestSkipped();
    }

    public function testHandleUpdateCommand()
    {
        $this->markTestSkipped();
        $event      = new Event(Uuid::uuid4(), Calendar::create('test'), EventType::single(), new DateTime('12:00'), new DateTime('13:00'), 'some Title', new Repetitions());
        $occurrence = new Occurrence(Uuid::uuid4(), $event, new DateTime('+1 hour'), new OccurrenceDuration(60));

        $command = UpdateOccurrenceCommand::fromArray([
             'occurrenceId' => $occurrence->id()->toString(),
             'method'       => OccurrenceUpdateManager::MODE_SINGLE,
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

        $handler = new OccurrenceUpdateManager($eventRepository, $occurrenceRepository);
        $handler->addStrategy(OccurrenceUpdateManager::MODE_SINGLE, $strategyMock->reveal());

        $handler->handle($command);
    }

    /**
     * @throws \Exception
     * @expectedException \Exception
     * @expectedExceptionMessage Mode 'single' not allowed. Only  allowed.
     */
    public function testStrategyNotSetException()
    {
        $this->markTestSkipped();
        $command = new UpdateOccurrenceCommand(
            '',
            OccurrenceUpdateManager::MODE_SINGLE,
            new DateTime('12:00'),
            new DateTime('13:00'),
            '',
            []
        );

        $handler = new OccurrenceUpdateManager(new InMemoryEventRepository(), new InMemoryOccurrenceRepository());

        $handler->handle($command);
    }
}
