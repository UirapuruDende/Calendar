<?php
namespace Dende\Calendar\UserInterface\Symfony\CalendarBundle\Tests\Integration;

use DateTime;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Generator\InMemory\IdGenerator;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration as EventDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\Duration as OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryEventByWeekSpecification;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryOccurrenceByWeekSpecification;
use Dende\Calendar\Tests\AssertDatesEqualTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EventTest
 * @package Gyman\Domain\Tests\Unit\Model
 */
class CalendarTest extends \PHPUnit_Framework_TestCase
{
    use AssertDatesEqualTrait;

    /**
     * @var OccurrenceFactory
     */
    private $occurrenceFactory;

    public function setUp(){
        $this->occurrenceFactory = new OccurrenceFactory(new IdGenerator());
    }

    public function testGetEventsByDate()
    {
        $calendar = new Calendar(0);

        $event1 = new Event(
            1,
            $calendar,
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-09-01 12:00:00'),
            new DateTime('2015-09-30 13:30:00'),
            'first event',
            new Repetitions([
                Repetitions::MONDAY,
                Repetitions::WEDNESDAY,
                Repetitions::FRIDAY,
            ]),
            new EventDuration(90),
            null
        );

        $event2 = new Event(
            2,
            $calendar,
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-09-01 12:15:00'),
            new DateTime('2015-09-30 13:00:00'),
            'second event',
            new Repetitions([
                Repetitions::WEDNESDAY,
            ]),
            new EventDuration(90),
            null
        );

        $event3 = new Event(
            3,
            $calendar,
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-09-01 12:15:00'),
            new DateTime('2015-09-30 13:00:00'),
            'third event',
            new Repetitions([
                Repetitions::MONDAY,
            ]),
            new EventDuration(90),
            null
        );

        $event4 = new Event(
            4,
            $calendar,
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-09-01 12:15:00'),
            new DateTime('2015-09-30 13:00:00'),
            'fourth event',
            new Repetitions([
                Repetitions::MONDAY,
                Repetitions::WEDNESDAY,
                Repetitions::FRIDAY,
            ]),
            new EventDuration(90),
            null
        );

        $event5 = new Event(
            5,
            $calendar,
            new EventType(EventType::TYPE_WEEKLY),
            new DateTime('2015-10-01 12:15:00'),
            new DateTime('2015-10-30 13:00:00'),
            'fifth event',
            new Repetitions([
                Repetitions::MONDAY,
                Repetitions::WEDNESDAY,
                Repetitions::FRIDAY,
            ]),
            new EventDuration(90),
            null
        );

        $eventRepository = new InMemoryEventRepository();
        $eventRepository->insert($event1);
        $eventRepository->insert($event2);
        $eventRepository->insert($event3);
        $eventRepository->insert($event4);
        $eventRepository->insert($event5);

        $eventCollection = $eventRepository->query(
            new InMemoryEventByWeekSpecification(2015, 38)
        );

        $this->assertCount(4, $eventCollection);

        $occurrenceRepository = new InMemoryOccurrenceRepository();

        foreach ($eventCollection as $event) {
            $occurrencesCollection = $this->occurrenceFactory->generateCollectionFromEvent($event);
            foreach ($occurrencesCollection as $occurrence) {
                $occurrenceRepository->insert($occurrence);
            }
        }

        $occurenceCollection = $occurrenceRepository->query(
            new InMemoryOccurrenceByWeekSpecification(2015, 38)
        );

        $this->assertCount(8, $occurenceCollection);

        $this->assertDatesEqual($occurenceCollection[0]->startDate(), '2015-09-14 12:00:00');
        $this->assertEquals($occurenceCollection[0]->event()->id(), 1);

        $this->assertDatesEqual($occurenceCollection[1]->startDate(), '2015-09-14 12:15:00');
        $this->assertEquals($occurenceCollection[1]->event()->id(), 3);

        $this->assertDatesEqual($occurenceCollection[2]->startDate(), '2015-09-14 12:15:00');
        $this->assertEquals($occurenceCollection[2]->event()->id(), 4);

        $this->assertDatesEqual($occurenceCollection[3]->startDate(), '2015-09-16 12:00:00');
        $this->assertEquals($occurenceCollection[3]->event()->id(), 1);

        $this->assertDatesEqual($occurenceCollection[4]->startDate(), '2015-09-16 12:15:00');
        $this->assertEquals($occurenceCollection[4]->event()->id(), 2);

        $this->assertDatesEqual($occurenceCollection[5]->startDate(), '2015-09-16 12:15:00');
        $this->assertEquals($occurenceCollection[5]->event()->id(), 4);

        $this->assertDatesEqual($occurenceCollection[6]->startDate(), '2015-09-18 12:00:00');
        $this->assertEquals($occurenceCollection[6]->event()->id(), 1);

        $this->assertDatesEqual($occurenceCollection[7]->startDate(), '2015-09-18 12:15:00');
        $this->assertEquals($occurenceCollection[7]->event()->id(), 4);
    }
}
