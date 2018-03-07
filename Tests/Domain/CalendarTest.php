<?php
namespace Dende\Calendar\Tests\Domain\Calendar;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CalendarTest extends TestCase
{
    public function testAddEvent()
    {
        $calendar = new Calendar(Uuid::uuid4(), 'test');

        $eventId = Uuid::uuid4();

        $calendar->addEvent(Event::create($eventId, 'test', new DateTime(), new DateTime('+5 minutes'), EventType::single(), new Repetitions, $calendar));

        $this->assertCount(1, $calendar->events());
        $this->assertTrue($calendar->events()->first()->id()->equals($eventId));
        $this->assertNotNull($calendar->getEventById($eventId));
    }
}
