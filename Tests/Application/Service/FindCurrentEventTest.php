<?php
namespace Dende\Calendar\Tests\Application\Service\FindCurrentEventTest;

use Carbon\Carbon;
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
        $baseTime = Carbon::instance(new DateTime("today 11:00"));
        $calendar = new Calendar(CalendarId::create(), 'title');

        $eventId = EventId::create();

        $calendar->addEvent(
            $eventId,
            'title',
            $baseTime->copy()->modify("-10 minutes"),
            $baseTime->copy()->modify("+ 10 minutes"),
            EventType::single(),
            Repetitions::none()
        );

        $occurrences = $calendar->getEventById($eventId)->occurrences();

        $occurrenceRepository = new InMemoryOccurrenceRepository($occurrences);

        $service       = new FindCurrentEvent($occurrenceRepository);
        $currentEvents = $service->getCurrentEvents($calendar, $baseTime->copy());

        $this->assertEquals($eventId, $currentEvents->first()->id());
    }
}
