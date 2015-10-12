<?php
namespace Dende\Calendar\UserInterface\Symfony\CalendarBundle\Tests\Integration;

class InMemoryOccurrenceRepositoryTestCase extends \PHPUnit_Framework_TestCase
{
    //    public function testGetOccurenceByDateWeekly()
//    {
//        $event = new Event(
//            new EventId(0),
//            new Calendar(
//                new CalendarId(0),
//                'calendar-title'
//            ),
//            new EventType(EventType::TYPE_WEEKLY),
//            new DateTime('2015-09-01 12:00:00'),
//            new DateTime('2015-09-07 13:30:00'),
//            'some title',
//            new Repetitions([
//                Repetitions::MONDAY,
//                Repetitions::TUESDAY,
//                Repetitions::WEDNESDAY,
//                Repetitions::THURSDAY,
//                Repetitions::FRIDAY,
//                Repetitions::SATURDAY,
//                Repetitions::SUNDAY,
//            ]),
//            new Duration(90),
//            new ArrayCollection()
//        );
//
//        $event->resetAllOccurrences();
//
//        $totalOccurences = $event->occurrences();
//        $occurence = $event->getOccurenceByDate(new \DateTime('2015-09-01 12:30:00'));
//
//        $this->assertNotNull($occurence);
//        $this->assertCount(7, $totalOccurences);
//        $this->assertInstanceOf('Dende\Calendar\Model\Occurrence', $occurence);
//    }

//    public function testGetOccurenceByDateSingle()
//    {
//        $event = new Event(
//            new EventId(0),
//            new Calendar(
//                new CalendarId(0),
//                'calendar-title'
//            ),
//            new EventType(EventType::TYPE_SINGLE),
//            new DateTime('2015-09-01 12:00:00'),
//            new DateTime('2015-09-07 13:30:00'),
//            'some title',
//            new Repetitions([]),
//            new Duration(90),
//            new ArrayCollection()
//        );
//
//        $event->resetAllOccurrences();
//
//        $totalOccurences = $event->occurrences();
//        $occurence = $event->getOccurenceByDate(new \DateTime('2015-09-01 12:30:00'));
//
//        $this->assertNotNull($occurence);
//        $this->assertCount(1, $totalOccurences);
//        $this->assertInstanceOf('Dende\Calendar\Model\Occurrence', $occurence);
//        $this->assertDatesEqual($occurence->startDate(), '2015-09-01 12:00:00');
//        $this->assertDatesEqual($occurence->endDate(), '2015-09-01 13:30:00');
//    }

//    public function testGetOccurenceByDateReturnsNothingWeekly()
//    {
//        $event = new Event(
//            new EventId(0),
//            new Calendar(
//                new CalendarId(0),
//                'calendar-title'
//            ),
//            new EventType(EventType::TYPE_WEEKLY),
//            new DateTime('2015-09-01 12:00:00'),
//            new DateTime('2015-09-30 13:30:00'),
//            'some title',
//            new Repetitions([
//                Repetitions::MONDAY,
//                Repetitions::WEDNESDAY,
//                Repetitions::FRIDAY,
//            ]),
//            new Duration(90),
//            new ArrayCollection()
//        );
//
//        $event->resetAllOccurrences();
//
//        $totalOccurences = $event->occurrences();
//        $occurence = $event->getOccurenceByDate(new \DateTime('2015-09-01 12:30:00'));
//
//        $this->assertNull($occurence);
//        $this->assertCount(13, $totalOccurences);
//    }

//    public function testGetOccurenceByDateReturnsNothingSingle()
//    {
//        $event = new Event(
//            new EventId(0),
//            new Calendar(
//                new CalendarId(0),
//                'calendar-title'
//            ),
//            new EventType(EventType::TYPE_SINGLE),
//            new DateTime('now'),
//            new DateTime('tomorrow'),
//            'some title',
//            new Repetitions([]),
//            new Duration(90),
//            new ArrayCollection()
//        );
//
//        $event->resetAllOccurrences();
//
//        $totalOccurences = $event->occurrences();
//        $occurence = $event->getOccurenceByDate(new \DateTime('yesterday'));
//
//        $this->assertNull($occurence);
//        $this->assertCount(1, $totalOccurences);
//    }

//    public function testGetCurrentOccurenceSingle()
//    {
//        $event = new Event(
//            new EventId(0),
//            new Calendar(
//                new CalendarId(0),
//                'calendar-title'
//            ),
//            new EventType(EventType::TYPE_SINGLE),
//            new DateTime('now'),
//            new DateTime('tomorrow'),
//            'some title',
//            new Repetitions([]),
//            new Duration(24 * 60),
//            new ArrayCollection()
//        );
//
//        $event->resetAllOccurrences();
//
//        $totalOccurences = $event->occurrences();
//        $occurence = $event->getCurrentOccurrence();
//
//        $this->assertNotNull($occurence);
//        $this->assertCount(1, $totalOccurences);
//        $this->assertDatesEqual($occurence->startDate(), 'now');
//        $this->assertDatesEqual($occurence->endDate(), '+24 hours');
//    }

//    public function testGetCurrentOccurenceWeekly()
//    {
//        $event = new Event(
//            new EventId(0),
//            new Calendar(
//                new CalendarId(0),
//                'calendar-title'
//            ),
//            new EventType(EventType::TYPE_WEEKLY),
//            new DateTime('-1 day 00:00:00'),
//            new DateTime('+1 day 23:59:59'),
//            'some title',
//            new Repetitions([
//                Repetitions::MONDAY,
//                Repetitions::TUESDAY,
//                Repetitions::WEDNESDAY,
//                Repetitions::THURSDAY,
//                Repetitions::FRIDAY,
//                Repetitions::SATURDAY,
//                Repetitions::SUNDAY,
//            ]),
//            new Duration(24 * 60),
//            new ArrayCollection()
//        );
//
//        $event->resetAllOccurrences();
//
//        $totalOccurences = $event->occurrences();
//        $occurence = $event->getCurrentOccurrence();
//
//        $this->assertNotNull($occurence);
//        $this->assertCount(3, $totalOccurences);
//    }

//    public function testIsOngoingSingle()
//    {
//        $event = new Event(
//            new EventId(0),
//            new Calendar(
//                new CalendarId(0),
//                'calendar-title'
//            ),
//            new EventType(EventType::TYPE_SINGLE),
//            new DateTime('now'),
//            new DateTime('+60 minutes'),
//            'some title',
//            new Repetitions([]),
//            new Duration(60),
//            new ArrayCollection()
//        );
//
//        $event->resetAllOccurrences();
//
//        $totalOccurences = $event->occurrences();
//
//        $this->assertTrue($event->isOngoing());
//        $this->assertCount(1, $totalOccurences);
//    }

//    public function testIsOngoingWeekly()
//    {
//        $event = new Event(
//            new EventId(0),
//            new Calendar(
//                new CalendarId(0),
//                'calendar-title'
//            ),
//            new EventType(EventType::TYPE_WEEKLY),
//            new DateTime('now'),
//            new DateTime('+10 minutes'),
//            'some title',
//            new Repetitions([
//                Repetitions::MONDAY,
//                Repetitions::TUESDAY,
//                Repetitions::WEDNESDAY,
//                Repetitions::THURSDAY,
//                Repetitions::FRIDAY,
//                Repetitions::SATURDAY,
//                Repetitions::SUNDAY,
//            ]),
//            new Duration(10),
//            new ArrayCollection()
//        );
//
//        $event->resetAllOccurrences();
//
//        $totalOccurences = $event->occurrences();
//
//        $this->assertTrue($event->isOngoing());
//        $this->assertCount(1, $totalOccurences);
//
//        $event = new Event(
//            new EventId(0),
//            new Calendar(
//                new CalendarId(0),
//                'calendar-title'
//            ),
//            new EventType(EventType::TYPE_WEEKLY),
//            new DateTime('+10 minutes'),
//            new DateTime('+20 minutes'),
//            'some title',
//            new Repetitions([
//                Repetitions::MONDAY,
//                Repetitions::TUESDAY,
//                Repetitions::WEDNESDAY,
//                Repetitions::THURSDAY,
//                Repetitions::FRIDAY,
//                Repetitions::SATURDAY,
//                Repetitions::SUNDAY,
//            ]),
//            new Duration(10),
//            new ArrayCollection()
//        );
//
//        $event->resetAllOccurrences();
//
//        $totalOccurences = $event->occurrences();
//
//        $this->assertFalse($event->isOngoing());
//        $this->assertCount(1, $totalOccurences);
//    }
}
