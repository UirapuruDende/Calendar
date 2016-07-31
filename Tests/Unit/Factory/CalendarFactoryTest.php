<?php
namespace Dende\Calendar\UserInterface\Symfony\CalendarBundle\Tests\Unit\Factory;

use Dende\Calendar\Application\Factory\CalendarFactory;
use Dende\Calendar\Application\Generator\IdGeneratorInterface;
use Dende\Calendar\Domain\Calendar;
use Mockery as m;

class CalendarFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate() {
        $idGenerator = m::mock(IdGeneratorInterface::class);
        $idGenerator->shouldReceive("generateId")->andReturn("test");

        $factory = new CalendarFactory($idGenerator);
        $calendar = $factory->create();

        $this->assertInstanceOf(Calendar::class, $calendar);
        $this->assertEquals("test", $calendar->id());
    }
}
