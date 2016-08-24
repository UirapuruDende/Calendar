<?php
namespace Unit\Domain\Calendar;

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
            "single to single update" => $this->simpleUpdateData()
        ];
    }

    private function simpleUpdateData()
    {
        $originalCalendar= new Calendar(null, "title1");
        $originalType = new EventType(EventType::TYPE_SINGLE);
        $originalStartDate = new DateTime("yesterday 12:00");
        $originalEndDate = new DateTime("tomorrow 13:30");
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
}