<?php
namespace Dende\Calendar\Tests\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Handler\CreateEventHandler;
use Dende\Calendar\Application\Handler\UpdateEventHandler;
use Dende\Calendar\Application\Handler\UpdateStrategy\AllExclusive;
use Dende\Calendar\Application\Handler\UpdateStrategy\AllInclusive;
use Dende\Calendar\Application\Handler\UpdateStrategy\Overwrite;
use Dende\Calendar\Application\Handler\UpdateStrategy\Single;
use Dende\Calendar\Application\Service\FindCurrentEvent;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryEventByWeekSpecification;
use Dende\Calendar\Infrastructure\Persistence\InMemory\Specification\InMemoryOccurrenceByCalendarSpecification;
use Doctrine\Common\Collections\Criteria;
use Exception;

/**
 * Class ScheduleContext
 * @package Gyman\Domain\Tests\Context
 */
final class CalendarContext implements Context
{
    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * @var CreateEventHandler
     */
    private $createEventHandler;

    /**
     * @var UpdateEventHandler
     */
    private $updateEventHandler;

    /**
     * @var InMemoryEventRepository
     */
    private $eventRepository;

    /**
     * @var InMemoryOccurrenceRepository
     */
    private $occurrenceRepository;

    /**
     * @BeforeScenario
     */
    public function prepareUseCases()
    {
        $this->calendar = new Calendar(new CalendarId(0), 'calendar-title');
        $this->eventRepository = new InMemoryEventRepository();
        $this->occurrenceRepository = new InMemoryOccurrenceRepository();
        $this->createEventHandler = new CreateEventHandler(
            $this->eventRepository,
            $this->occurrenceRepository
        );

        $updateEventHandler = new UpdateEventHandler(
            $this->eventRepository,
            $this->occurrenceRepository
        );

        $updateEventHandler->setStrategies([
            UpdateEventHandler::MODE_SINGLE        => new Single(),
            UpdateEventHandler::MODE_OVERWRITE => new Overwrite(),
        ]);

        $this->updateEventHandler = $updateEventHandler;
    }

    /**
     * @Given /^I have calendar created$/
     */
    public function iHaveCalendarCreated()
    {
    }

    /**
     * @When /^I add new calendar event with data$/
     */
    public function iAddNewCalendarEventWithData(TableNode $table)
    {
        foreach ($table as $row) {
            $repetitions = [];

            $days = array_map('trim', explode(',', $row['repetition']));

            if ($days[0] != '-') {
                foreach ($days as $day) {
                    $repetitions[] = Carbon::parse('last ' . $day)->dayOfWeek;
                }
            }

            $command = new CreateEventCommand();
            $command->calendar = $this->calendar;
            $command->type = $row['type'];
            $command->startDate = Carbon::parse($row['startDate']);
            $command->endDate = Carbon::parse($row['endDate']);
            $command->duration = $row['duration'];
            $command->title = $row['title'];
            $command->repetitionDays = $repetitions;

            $this->createEventHandler->handle($command);
        }
    }

    /**
     * @Then /^calendar has (\d+) events$/
     */
    public function calendarHasEvent($count)
    {
        if ($this->calendar->events()->count() != $count) {
            throw new Exception(
                sprintf('Expected that calendar has %d events, actually has %d event', $count, $this->calendar->events()->count())
            );
        }
    }

    /**
     * @Given /^event \'([^\']*)\' has (\d+) occurrence with data$/
     */
    public function eventHasOccurrenceWithData($eventId, $count, TableNode $table)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('id', $eventId));
        $events = $this->calendar->events()->matching($criteria);

        if ($events->count() != $count) {
            throw new Exception(
                sprintf('Event not found')
            );
        }
    }

    /**
     * @Given /^current event has title \'([^\']*)\'$/
     */
    public function currentEventHasTitle($title)
    {
        $service = new FindCurrentEvent($this->occurrenceRepository);
        $event = $service->getCurrentEvent($this->calendar);

        if (!is_null($event) && $event->title() === $title) {
            return;
        }

        throw new \Exception(sprintf(
            "Current event should have title '%s' but actualy has '%s'",
            $title,
            $event->title()
        ));
    }

    /**
     * @Given /^calendar returns (\d+) event for current week$/
     */
    public function calendarReturnsEventForCurrentWeek($count)
    {
        $year = Carbon::create()->year;
        $week = Carbon::create()->weekOfYear;

        $events = $this->eventRepository->query(
            new InMemoryEventByWeekSpecification($year, $week)
        );

        if (count($events) === intval($count)) {
            return;
        }

        throw new \Exception(sprintf(
            'Expected %d events for current week, actually got %d events',
            $count,
            count($events)
        ));
    }

    /**
     * @Given /^calendar has (\d+) occurences$/
     */
    public function calendarHasOccurences($count)
    {
        $occurrences = $this->occurrenceRepository->query(
            new InMemoryOccurrenceByCalendarSpecification($this->calendar)
        );

        if (count($occurrences) === intval($count)) {
            return;
        }

        throw new \Exception(sprintf(
            'Expected %d occurrences for calendar, actually got %d occurrences',
            $count,
            count($occurrences)
        ));
    }

    /**
     * @Given /^calendar returns (\d+) events for date range from "([^"]*)" to "([^"]*)"$/
     */
    public function calendarReturnsEventsForDateRangeFromTo($count, $startDate, $endDate)
    {
        $events = $this->eventRepository->findAllByCalendarInDateRange(
            new DateTime($startDate),
            new DateTime($endDate),
            $this->calendar
        );

        if (count($events) === intval($count)) {
            return;
        }

        throw new \Exception(sprintf(
            'Expected %d occurrences for calendar, actually got %d occurrences',
            $count,
            count($events)
        ));
    }

    /**
     * @Given /^calendar returns (\d+) occurrences for date range from "([^"]*)" to "([^"]*)"$/
     */
    public function calendarReturnsOccurrencesForDateRangeFromTo($count, $startDate, $endDate)
    {
        $occurrences = $this->occurrenceRepository->findAllByCalendarInDateRange(
            new DateTime($startDate),
            new DateTime($endDate),
            $this->calendar
        );

        if (count($occurrences) === intval($count)) {
            return;
        }

        throw new \Exception(sprintf(
            'Expected %d occurrences for calendar, actually got %d occurrences',
            $count,
            count($occurrences)
        ));
    }

    /**
     * @Given /^there are (\d+) occurrences$/
     */
    public function thereAreOccurrences($count)
    {
        /** @var Occurrence[] $occurrences */
        $occurrences = $this->occurrenceRepository->findAllByCalendar($this->calendar);

        if (count($occurrences) === intval($count)) {
            return;
        }

        throw new \Exception(sprintf(
            'Expected %d occurrences for calendar, actually got %d occurrences',
            $count,
            count($occurrences)
        ));
    }

    /**
     * @Then /^event with title \'([^\']*)\' has data$/
     */
    public function eventWithTitleHasData($title, TableNode $table)
    {
        /** @var Event $event */
        $event = current($this->eventRepository->findOneByTitle($title));

        if(!$event instanceof Event)
        {
            throw new \Exception(sprintf("Event with title '%s' not found!", $title));
        }

        foreach ($table as $row) {
            $this->assertEventEqualsRow($event, $row);
        }
    }

    /**
     * @Given /^I update occurrence \'(\d+)\' of event with title \'([^\']*)\' with data in \'([^\']*)\' mode$/
     */
    public function iUpdateOccurrenceWithDataInMode($index, $title, $mode, TableNode $table)
    {
        $event = current($this->eventRepository->findOneByTitle($title));
        $occurrences = $this->occurrenceRepository->findAllByEvent($event);

        $occurrence = current(array_slice($occurrences->toArray(), $index, 1));

        foreach ($table as $row) {
            $repetitions = [];

            $days = array_map('trim', explode(',', $row['repetition']));

            if ($days[0] != '-') {
                foreach ($days as $day) {
                    $repetitions[] = Carbon::parse('last ' . $day)->dayOfWeek;
                }
            }

            $command = new UpdateEventCommand();
            $command->occurrence = $occurrence;
            $command->method = $mode;
            $command->startDate = new DateTime($row['startDate']);
            $command->endDate = new DateTime($row['endDate']);
            $command->duration = $row['duration'];
            $command->repetitionDays = $repetitions;
            $command->title = $row['title'];
            $command->type = $row['type'];
            $command->calendar = $this->calendar;

            $this->updateEventHandler->handle($command);
        }
    }

    /**
     * @Given /^occurence of single event with title \'([^\']*)\' has data$/
     */
    public function occurenceOfEventWithTitleHasData($title, TableNode $table)
    {
        /** @var Event $event */
        $event = current($this->eventRepository->findOneByTitle($title));
        $occurrence = current($event->occurrences()->toArray());

        foreach ($table as $row) {
            $this->assertOccurrenceEqualsRow($occurrence, $row);
        }
    }

    /**
     * @Given /^occurences of event with title \'([^\']*)\' should have data$/
     */
    public function occurencesOfEventWithTitleShouldHaveData($title, TableNode $table)
    {
        $event = current($this->eventRepository->findOneByTitle($title));

        if(!$event instanceof Event)
        {
            throw new \Exception(sprintf("Event with title '%s' not found!", $title));
        }

        $occurrences = $event->occurrences();

        die(var_dump($occurrences));

        foreach ($occurrences as $key => $occurrence) {
            echo $key;
        }
    }

    /**
     * @param Occurrence $occurrence
     * @param array $row
     * @throws Exception
     */
    private function assertOccurrenceEqualsRow(Occurrence $occurrence, array $row)
    {
        if (!Carbon::instance($occurrence->startDate())->eq(Carbon::parse($row['startDate']))) {
            throw new Exception(sprintf(
                'Expected startDate was %s actually is %s',
                Carbon::parse($row['startDate'])->format('Y-m-d H:i:s'),
                $occurrence->startDate()->format('Y-m-d H:i:s')
            ));
        }

        if (!Carbon::instance($occurrence->endDate())->eq(Carbon::parse($row['endDate']))) {
            throw new Exception(sprintf(
                'Expected endDate was %s actually is %s',
                Carbon::parse($row['endDate'])->format('Y-m-d H:i:s'),
                $occurrence->endDate()->format('Y-m-d H:i:s')
            ));
        }

        if ($occurrence->duration()->minutes() !== (int) $row['duration']) {
            throw new Exception(sprintf(
                'Expected duration was %s actually is %s',
                $row['duration'],
                $occurrence->duration()->minutes()
            ));
        }
    }

    /**
     * @param Event $event
     * @param array $row
     * @throws Exception
     */
    private function assertEventEqualsRow(Event $event, array $row)
    {
        $repetitions = [];

        $days = array_map('trim', explode(',', $row['repetition']));

        if ($days[0] != '-') {
            foreach ($days as $day) {
                $repetitions[] = Carbon::parse('last ' . $day)->dayOfWeek;
            }
        }

        if (!$event->type()->isType($row['type'])) {
            throw new Exception(sprintf(
                'Expected type was %s actually is %s',
                $row['type'],
                $event->type()->type()
            ));
        }

        if (!Carbon::instance($event->startDate())->eq(Carbon::parse($row['startDate']))) {
            throw new Exception(sprintf(
                'Expected startDate was %s actually is %s',
                Carbon::parse($row['startDate'])->format('Y-m-d H:i:s'),
                $event->startDate()->format('Y-m-d H:i:s')
            ));
        }

        if (!Carbon::instance($event->endDate())->eq(Carbon::parse($row['endDate']))) {
            throw new Exception(sprintf(
                'Expected endDate was %s actually is %s',
                Carbon::parse($row['endDate'])->format('Y-m-d H:i:s'),
                $event->endDate()->format('Y-m-d H:i:s')
            ));
        }

        if ($event->title() !== $row['title']) {
            throw new Exception(sprintf(
                'Expected title was %s actually is %s',
                $row['title'],
                $event->title()
            ));
        }

        if ($event->duration()->minutes() !== (int) $row['duration']) {
            throw new Exception(sprintf(
                'Expected duration was %s actually is %s',
                $row['duration'],
                $event->duration()->minutes()
            ));
        }

        if (!$event->repetitions()->sameDays($repetitions)) {
            throw new Exception(sprintf(
                'Expected repetitions was %s actually is %s',
                implode(', ', $repetitions),
                implode(', ', $event->repetitions()->weekly())
            ));
        }
    }
}
