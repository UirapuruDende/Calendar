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
     *
     * @param $params
     */
    public function testUpdateEventWithCommand($params, $command, $expectedValues)
    {
        $event = new Event(null, ...array_values($params));

        $event->updateWithCommand($command);

        $this->assertEquals($event->startDate(), $expectedValues['startDate'], null, 2);
        $this->assertEquals($event->endDate(), $expectedValues['endDate'], null, 2);
        $this->assertEquals($event->title(), $expectedValues['title']);
        $this->assertEquals($event->repetitions()->weekdays(), $expectedValues['repetitions']);
        $this->assertEquals($event->duration()->minutes(), $expectedValues['duration']);
    }

    public function dataProvider()
    {
        return [
            'single to single update' => $this->simpleUpdateData(),
            'single to weekly update' => $this->singleToWeeklyUpdateData(),
            'weekly to single update' => $this->weeklyToSingleUpdateData(),
            'weekly to weekly update' => $this->weeklyToWeeklyUpdateData(),

        ];
    }

    private function simpleUpdateData()
    {
        $originalCalendar = new Calendar(null, 'title1');
        $originalType = new EventType(EventType::TYPE_SINGLE);
        $originalStartDate = new DateTime('today 12:00');
        $originalEndDate = new DateTime('today 13:30');
        $originalRepetitions = new Event\Repetitions([]);
        $originalTitle = 'Test Title';

        $updateCommand = new UpdateEventCommand();
        $updateCommand->startDate = $originalStartDate;
        $updateCommand->endDate = $originalEndDate;
        $updateCommand->title = $originalTitle;
        $updateCommand->repetitionDays = $originalRepetitions->weekdays();

        return [
            'params' => [
                'calendar'    => $originalCalendar,
                'type'        => $originalType,
                'startDate'   => $originalStartDate,
                'endDate'     => $originalEndDate,
                'title'       => $originalTitle,
                'repetitions' => $originalRepetitions,
            ],
            'command'        => $updateCommand,
            'expectedValues' => [
                'calendar'    => $originalCalendar,
                'type'        => $originalType->type(),
                'startDate'   => $originalStartDate,
                'endDate'     => $originalEndDate,
                'title'       => $originalTitle,
                'repetitions' => $originalRepetitions->weekdays(),
                'duration'    => 90,
            ],
        ];
    }

    private function singleToWeeklyUpdateData()
    {
        $originalCalendar = new Calendar(null, 'title1');
        $originalType = new EventType(EventType::TYPE_SINGLE);
        $originalStartDate = new DateTime('today 12:00');
        $originalEndDate = new DateTime('today 13:30');
        $originalRepetitions = new Event\Repetitions([]);
        $originalTitle = 'Test Title';
        $originalDuration = new Duration(90);

        $updateCommand = new UpdateEventCommand();
        $updateCommand->startDate = $originalStartDate;
        $updateCommand->endDate = Carbon::instance($originalEndDate)->addMonth();
        $updateCommand->title = 'New Title';
        $updateCommand->repetitionDays = [2, 3, 4];

        return [
            'params' => [
                'calendar'    => $originalCalendar,
                'type'        => $originalType,
                'startDate'   => $originalStartDate,
                'endDate'     => $originalEndDate,
                'title'       => $originalTitle,
                'repetitions' => $originalRepetitions,
            ],
            'command'        => $updateCommand,
            'expectedValues' => [
                'calendar'    => $originalCalendar,
                'type'        => EventType::TYPE_WEEKLY,
                'startDate'   => new DateTime('today 12:00'),
                'endDate'     => new DateTime('+1 month 13:30'),
                'title'       => 'New Title',
                'repetitions' => [2, 3, 4],
                'duration'    => 90,
            ],
        ];
    }

    private function weeklyToSingleUpdateData()
    {
        $originalCalendar = new Calendar(null, 'title1');
        $originalType = new EventType(EventType::TYPE_WEEKLY);
        $originalStartDate = new DateTime('2016-08-01 12:00');
        $originalEndDate = new DateTime('2016-08-31 13:30');
        $originalRepetitions = new Event\Repetitions([1, 3, 5]);
        $originalTitle = 'Test Title';
        $originalDuration = new Duration(90);

        $updateCommand = new UpdateEventCommand();
        $updateCommand->startDate = new DateTime('2016-08-15 12:00');
        $updateCommand->endDate = new DateTime('2016-08-15 13:00');
        $updateCommand->title = 'New Title';
        $updateCommand->repetitionDays = [];

        return [
            'params' => [
                'calendar'    => $originalCalendar,
                'type'        => $originalType,
                'startDate'   => $originalStartDate,
                'endDate'     => $originalEndDate,
                'title'       => $originalTitle,
                'repetitions' => $originalRepetitions,
            ],
            'command'        => $updateCommand,
            'expectedValues' => [
                'calendar'    => $originalCalendar,
                'type'        => EventType::TYPE_SINGLE,
                'startDate'   => new DateTime('2016-08-15 12:00'),
                'endDate'     => new DateTime('2016-08-15 13:00'),
                'title'       => 'New Title',
                'repetitions' => [],
                'duration'    => 60,
            ],
        ];
    }

    private function weeklyToWeeklyUpdateData()
    {
        $newCalendar = new Calendar(null, 'new calendar');

        $originalCalendar = new Calendar(null, 'title1');
        $originalType = new EventType(EventType::TYPE_WEEKLY);
        $originalStartDate = new DateTime('2016-08-01 12:00');
        $originalEndDate = new DateTime('2016-08-31 13:30');
        $originalRepetitions = new Event\Repetitions([1, 3, 5]);
        $originalTitle = 'Test Title';
        $originalDuration = new Duration(90);

        $updateCommand = new UpdateEventCommand();
        $updateCommand->startDate = new DateTime('2016-09-01 12:00');
        $updateCommand->endDate = new DateTime('2016-09-30 13:00');
        $updateCommand->title = 'New Title';
        $updateCommand->repetitionDays = [2, 4];

        return [
            'params' => [
                'calendar'    => $originalCalendar,
                'type'        => $originalType,
                'startDate'   => $originalStartDate,
                'endDate'     => $originalEndDate,
                'title'       => $originalTitle,
                'repetitions' => $originalRepetitions,
            ],
            'command'        => $updateCommand,
            'expectedValues' => [
                'type'        => EventType::TYPE_WEEKLY,
                'startDate'   => new DateTime('2016-09-01 12:00'),
                'endDate'     => new DateTime('2016-09-30 13:00'),
                'title'       => 'New Title',
                'repetitions' => [2, 4],
                'duration'    => 60,
            ],
        ];
    }
}
