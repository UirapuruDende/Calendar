<?php
namespace Unit\Domain\Calendar;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;

class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     * @param $params
     */
    public function testUpdateEventWithCommand($params, $command, $expectedValues)
    {
        list($calendar, $type, $startDate, $endDate, $repetitions, $title, $duration) = array_values($params);

        $event = new Event(
            null,
            $calendar,
            $type,
            $startDate,
            $endDate,
            $title,
            $repetitions,
            $duration
        );

        $event->updateWithCommand($command);

        $this->assertEquals($event->calendar(), $expectedValues["calendar"]);
        $this->assertTrue($event->type()->isType($expectedValues["type"]));
        $this->assertEquals($event->startDate(), $expectedValues["startDate"]);
        $this->assertEquals($event->endDate(), $expectedValues["endDate"]);
        $this->assertEquals($event->title(), $expectedValues["title"]);
        $this->assertEquals($event->repetitions()->weekdays(), $expectedValues["repetitions"]);
        $this->assertEquals($event->duration()->minutes(), $expectedValues["duration"]);
    }

    public function dataProvider()
    {
        return [
            "single to single update" => $this->simpleUpdateData(),
            "single to weekly update" => $this->singleToWeeklyUpdateData(),
            "weekly to single update" => $this->weeklyToSingleUpdateData(),
            "weekly to weekly update" => $this->weeklyToWeeklyUpdateData(),

        ];
    }

    private function simpleUpdateData()
    {
        $originalCalendar= new Calendar(null, "title1");
        $originalType = new EventType(EventType::TYPE_SINGLE);
        $originalStartDate = new DateTime("today 12:00");
        $originalEndDate = new DateTime("today 13:30");
        $originalRepetitions = new Event\Repetitions([]);
        $originalTitle = "Test Title";
        $originalDuration = new Duration(90);

        $updateCommand = new UpdateEventCommand();
        $updateCommand->type =  $originalType->type();
        $updateCommand->calendar = $originalCalendar;
        $updateCommand->startDate = $originalStartDate;
        $updateCommand->endDate = $originalEndDate;
        $updateCommand->duration = $originalDuration->minutes();
        $updateCommand->title = $originalTitle;
        $updateCommand->repetitionDays = $originalRepetitions->weekdays();

        return [
            "params" => [
                "calendar" => $originalCalendar,
                "type" => $originalType,
                "startDate" => $originalStartDate,
                "endDate" => $originalEndDate,
                "repetitions" => $originalRepetitions,
                "title" => $originalTitle,
                "duration" => $originalDuration,
            ],
            "command" => $updateCommand,
            "expectedValues" => [
                "calendar" => $originalCalendar,
                "type" => $originalType->type(),
                "startDate" => $originalStartDate,
                "endDate" => $originalEndDate,
                "repetitions" => $originalRepetitions->weekdays(),
                "title" => $originalTitle,
                "duration" => $originalDuration->minutes(),
            ],
        ];
    }

    private function singleToWeeklyUpdateData()
    {
        $originalCalendar= new Calendar(null, "title1");
        $originalType = new EventType(EventType::TYPE_SINGLE);
        $originalStartDate = new DateTime("today 12:00");
        $originalEndDate = new DateTime("today 13:30");
        $originalRepetitions = new Event\Repetitions([]);
        $originalTitle = "Test Title";
        $originalDuration = new Duration(90);

        $updateCommand = new UpdateEventCommand();
        $updateCommand->type =  EventType::TYPE_WEEKLY;
        $updateCommand->calendar = $originalCalendar;
        $updateCommand->startDate = $originalStartDate;
        $updateCommand->endDate = Carbon::instance($originalEndDate)->addMonth();
        $updateCommand->duration = 60;
        $updateCommand->title = "New Title";
        $updateCommand->repetitionDays = [2,3,4];

        return [
            "params" => [
                "calendar" => $originalCalendar,
                "type" => $originalType,
                "startDate" => $originalStartDate,
                "endDate" => $originalEndDate,
                "repetitions" => $originalRepetitions,
                "title" => $originalTitle,
                "duration" => $originalDuration,
            ],
            "command" => $updateCommand,
            "expectedValues" => [
                "calendar" => $originalCalendar,
                "type" => EventType::TYPE_WEEKLY,
                "startDate" => new DateTime("today 12:00"),
                "endDate" => new DateTime("+1 month 13:30"),
                "repetitions" => [2,3,4],
                "title" => "New Title",
                "duration" => 60,
            ],
        ];
    }

    private function weeklyToSingleUpdateData()
    {
        $originalCalendar= new Calendar(null, "title1");
        $originalType = new EventType(EventType::TYPE_WEEKLY);
        $originalStartDate = new DateTime("2016-08-01 12:00");
        $originalEndDate = new DateTime("2016-08-31 13:30");
        $originalRepetitions = new Event\Repetitions([1,3,5]);
        $originalTitle = "Test Title";
        $originalDuration = new Duration(90);

        $updateCommand = new UpdateEventCommand();
        $updateCommand->type =  EventType::TYPE_SINGLE;
        $updateCommand->calendar = $originalCalendar;
        $updateCommand->startDate = new DateTime("2016-08-15 12:00");
        $updateCommand->endDate = new DateTime("2016-08-15 13:00");
        $updateCommand->duration = 60;
        $updateCommand->title = "New Title";
        $updateCommand->repetitionDays = [];

        return [
            "params" => [
                "calendar" => $originalCalendar,
                "type" => $originalType,
                "startDate" => $originalStartDate,
                "endDate" => $originalEndDate,
                "repetitions" => $originalRepetitions,
                "title" => $originalTitle,
                "duration" => $originalDuration,
            ],
            "command" => $updateCommand,
            "expectedValues" => [
                "calendar" => $originalCalendar,
                "type" => EventType::TYPE_SINGLE,
                "startDate" => new DateTime("2016-08-15 12:00"),
                "endDate" => new DateTime("2016-08-15 13:00"),
                "repetitions" => [],
                "title" => "New Title",
                "duration" => 60,
            ],
        ];
    }

    private function weeklyToWeeklyUpdateData()
    {
        $newCalendar= new Calendar(null, "new calendar");

        $originalCalendar= new Calendar(null, "title1");
        $originalType = new EventType(EventType::TYPE_WEEKLY);
        $originalStartDate = new DateTime("2016-08-01 12:00");
        $originalEndDate = new DateTime("2016-08-31 13:30");
        $originalRepetitions = new Event\Repetitions([1,3,5]);
        $originalTitle = "Test Title";
        $originalDuration = new Duration(90);

        $updateCommand = new UpdateEventCommand();
        $updateCommand->type =  EventType::TYPE_WEEKLY;
        $updateCommand->calendar = $newCalendar;
        $updateCommand->startDate = new DateTime("2016-09-01 12:00");
        $updateCommand->endDate = new DateTime("2016-09-30 13:00");
        $updateCommand->duration = 60;
        $updateCommand->title = "New Title";
        $updateCommand->repetitionDays = [2,4];

        return [
            "params" => [
                "calendar" => $originalCalendar,
                "type" => $originalType,
                "startDate" => $originalStartDate,
                "endDate" => $originalEndDate,
                "repetitions" => $originalRepetitions,
                "title" => $originalTitle,
                "duration" => $originalDuration,
            ],
            "command" => $updateCommand,
            "expectedValues" => [
                "calendar" => $newCalendar,
                "type" => EventType::TYPE_WEEKLY,
                "startDate" => new DateTime("2016-09-01 12:00"),
                "endDate" => new DateTime("2016-09-30 13:00"),
                "repetitions" => [2,4],
                "title" => "New Title",
                "duration" => 60,
            ],
        ];
    }
}