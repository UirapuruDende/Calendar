<?php
namespace Dende\Calendar\Application\Command;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Domain\Calendar;

/**
 * Class CreateEventCommand.
 */
final class CreateEventCommand implements EventCommandInterface
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var Calendar
     */
    public $calendar;

    /**
     * @var string
     */
    public $newCalendarName;

    /**
     * @var DateTime|Carbon
     */
    public $startDate;

    /**
     * @var DateTime|Carbon
     */
    public $endDate;

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var array
     */
    public $repetitionDays = [];

    public static function fromArray(array $array = []) : CreateEventCommand
    {
        $command = new self();

        $array = array_merge(get_object_vars($command), $array);

        $command->calendar = $array['calendar'];
        $command->newCalendarName = $array['newCalendarName'];
        $command->type = $array['type'];
        $command->startDate = $array['startDate'];
        $command->endDate = $array['endDate'];
        $command->title = $array['title'];
        $command->repetitionDays = $array['repetitionDays'];

        return $command;
    }
}
