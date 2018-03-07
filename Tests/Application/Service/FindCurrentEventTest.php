<?php
namespace Dende\Calendar\Tests\Application\Service\FindCurrentEventTest;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Service\FindCurrentEvent;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class EventTest.
 */
class FindCurrentEventTest extends TestCase
{
    public function testGetCurrentEvent()
    {
        $baseTime = Carbon::instance(new DateTime('today 11:00'));
        $calendar = new Calendar(Uuid::uuid4(), 'title');

        $eventId = Uuid::uuid4();

        $calendar->addEvent(Event::create(
            $eventId,
            'title',
            $baseTime->copy()->modify('-10 minutes'),
            $baseTime->copy()->modify('+ 10 minutes'),
            EventType::single(),
            Repetitions::none(),
            $calendar
        ));

        $occurrences = $calendar->getEventById($eventId)->occurrences();

        $occurrenceRepository = new InMemoryOccurrenceRepository($occurrences);

        $service       = new FindCurrentEvent($occurrenceRepository);
        $currentEvents = $service->getCurrentEvents($calendar, $baseTime->copy());

        $this->assertEquals($eventId, $currentEvents->first()->id());
    }
}
