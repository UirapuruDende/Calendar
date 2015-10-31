<?php
namespace Dende\Calendar\Tests\Unit\Application\Service\FindCurrentEventTest;

use DateTime;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Generator\InMemory\IdGenerator;
use Dende\Calendar\Application\Service\FindCurrentEvent;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Mockery as m;

/**
 * Class EventTest
 * @package Gyman\Domain\Tests\Unit\Model
 */
class FindCurrentEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCurrentEvent()
    {
        $calendar = new Calendar(0, 'title');

        $event = (new EventFactory(new IdGenerator()))->createFromArray([
            'id'          => 10,
            'type'        => new EventType(EventType::TYPE_SINGLE),
            'startDate'   => new DateTime('-10 minutes'),
            'endDate'     => new DateTime('+10 minutes'),
            'duration'    => new Duration(10),
            'repetitions' => new Repetitions([]),
            'calendar'    => $calendar,
        ]);

        $occurrences = (new OccurrenceFactory(new IdGenerator()))->generateCollectionFromEvent($event);

        $occurrenceRepository = m::mock("Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface");
        $occurrenceRepository->shouldReceive('findOneByDateAndCalendar')->andReturn($occurrences);

        $service = new FindCurrentEvent($occurrenceRepository);
        $currentEvent = $service->getCurrentEvent($calendar);

        $this->assertEquals(10, $currentEvent->id());
    }

    public function tearDown()
    {
        m::close();
    }
}
