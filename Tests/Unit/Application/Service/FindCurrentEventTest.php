<?php
namespace Dende\Calendar\Tests\Unit\Application\Service\FindCurrentEventTest;

use DateTime;
use Dende\Calendar\Application\Service\FindCurrentEvent;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;

/**
 * Class EventTest.
 */
class FindCurrentEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCurrentEvent()
    {
        $calendar = new Calendar(CalendarId::create(), 'title');

        $eventId = EventId::create();

        $calendar->addEvent(
            $eventId,
            'title',
            new DateTime('-10 minutes'),
            new DateTime('+10 minutes'),
            EventType::single(),
            Repetitions::none()
        );

        $occurrences = $calendar->getEventById($eventId)->occurrences();

        $occurrenceRepository = new InMemoryOccurrenceRepository($occurrences);

        $service       = new FindCurrentEvent($occurrenceRepository);
        $currentEvents = $service->getCurrentEvents($calendar);

        $this->assertEquals($eventId, $currentEvents->first()->id());
    }
}
