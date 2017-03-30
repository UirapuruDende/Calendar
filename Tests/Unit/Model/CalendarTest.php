<?php
namespace Dende\Calendar\UserInterface\Symfony\CalendarBundle\Tests\Unit\Model;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use PHPUnit_Framework_TestCase;

/**
 * Class EventTest.
 */
class CalendarTest extends PHPUnit_Framework_TestCase
{

    public function testEventCreation()
    {
        $calendar = new Calendar(new CalendarId("1"), "test");

        $calendar->createEvent("single", EventType::single(), new DateTime(), new DateTime("+1 hour"), Repetitions::none());
        $calendar->createEvent("weekly", EventType::weekly(), new DateTime(), new DateTime("+7 days"), Repetitions::daily());

        $this->assertCount(2, $calendar->events());
        $this->assertCount(1, $calendar->events()->get(0)->occurrences());
        $this->assertCount(7, $calendar->events()->get(1)->occurrences());
    }

    public function testEventResize()
    {
        $calendar = new Calendar(new CalendarId("1"), "test");
        $calendar->createEvent("single", EventType::single(), new DateTime(), new DateTime("+1 hour"), Repetitions::none());
        $calendar->createEvent("single", EventType::single(), new DateTime(), new DateTime("+1 hour"), Repetitions::none());
        $calendar->createEvent("weekly", EventType::weekly(), new DateTime(), new DateTime("+7 days"), Repetitions::daily());

        $eventId = $calendar->events()->last()->getId();

        $calendar->resizeEvent($eventId, new DateTime(), new DateTime());
    }
}
