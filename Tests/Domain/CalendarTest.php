<?php
namespace Dende\Calendar\Tests\Domain\Calendar;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use PHPUnit_Framework_TestCase;

class CalendarTest extends PHPUnit_Framework_TestCase
{
    public function testAddEvent()
    {
        $calendar = new Calendar(CalendarId::create(), 'test');

        $eventId = EventId::create();

        $calendar->addEvent($eventId, 'test', new DateTime(), new DateTime('+5 minutes'), EventType::single());

        $this->assertCount(1, $calendar->events());
        $this->assertTrue($calendar->events()->first()->id()->equals($eventId));
        $this->assertNotNull($calendar->getEventById($eventId));
    }
}
