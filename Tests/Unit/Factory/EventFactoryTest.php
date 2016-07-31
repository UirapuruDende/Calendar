<?php
namespace Dende\Calendar\UserInterface\Symfony\CalendarBundle\Tests\Unit\Factory;

use DateTime;
use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Generator\IdGeneratorInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Mockery as m;

class EventFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate() {
        $idGenerator = m::mock(IdGeneratorInterface::class);
        $idGenerator->shouldReceive("generateId")->andReturn("test");

        $factory = new EventFactory($idGenerator);
        $event = $factory->create();

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals("test", $event->id());
    }

    public function testCreateFromCommand()
    {
        $idGenerator = m::mock(IdGeneratorInterface::class);
        $idGenerator->shouldReceive("generateId")->andReturn("test");

        $command = new CreateEventCommand();

        $command->type = Event\EventType::TYPE_SINGLE;
        $command->calendar = new Calendar(null, "");
        $command->startDate = new DateTime();
        $command->endDate = new DateTime();
        $command->title = "test";

        $factory = new EventFactory($idGenerator);
        $event = $factory->createFromCommand($command);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals("test", $event->title());
    }
}
