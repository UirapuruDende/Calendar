<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler;

use DateTime;
use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Application\Handler\CreateEventHandler;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class CreateEventHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleCreateCommand()
    {
        $calendar = new Calendar('test');

        $command = CreateEventCommand::fromArray([
            'calendar'  => $calendar,
            'startDate' => new DateTime('+1 hour'),
            'endDate'   => new DateTime('+1 day +3 hours'),
            'type'      => EventType::TYPE_SINGLE,
       ]);

        $event = $this->prophesize(Event::class);

        $eventRepositoryMock = $this->prophesize(EventRepositoryInterface::class);
        $occurrenceRepositoryMock = $this->prophesize(OccurrenceRepositoryInterface::class);
        $eventFactoryMock = $this->prophesize(EventFactoryInterface::class);
        $occurrenceFactoryMock = $this->prophesize(OccurrenceFactoryInterface::class);
        $occurrenceFactoryMock->createFromArray()->willReturn(new ArrayCollection([]));
        $eventFactoryMock->createFromCommand($command)->willReturn($event->reveal());

        $handler = new CreateEventHandler($eventRepositoryMock->reveal(), $occurrenceRepositoryMock->reveal(), $eventFactoryMock->reveal(), $occurrenceFactoryMock->reveal());
        $handler->handle($command);

        $this->assertEquals(new DateTime('+3 hours'), $command->endDate);
    }
}
