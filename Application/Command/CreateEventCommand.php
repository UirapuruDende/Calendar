<?php
namespace Dende\Calendar\Application\Command;

use Carbon\Carbon;
use DateTime;

/**
 * Class CreateEventCommand.
 */
final class CreateEventCommand implements EventCommandInterface
{
    /**
     * @var string
     */
    public $calendarId;

    /**
     * @var string
     */
    public $type;

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
    public $repetitions = [];

    public static function fromArray(array $array = []) : CreateEventCommand
    {
        $command = new self();

        $command->calendarId  = $array['calendarId'];
        $command->type        = $array['type'];
        $command->startDate   = $array['startDate'];
        $command->endDate     = $array['endDate'];
        $command->title       = $array['title'];
        $command->repetitions = $array['repetitions'];

        return $command;
    }
}
