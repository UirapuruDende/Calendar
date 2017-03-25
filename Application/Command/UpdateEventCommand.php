<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;

/**
 * Class CreateEventCommand.
 */
final class UpdateEventCommand implements EventCommandInterface, UpdateEventCommandInterface
{
    /**
     * Occurrence that was clicked to edit relating event.
     *
     * @var OccurrenceInterface
     */
    public $occurrence; // this should be changed to $occurrenceId

    /**
     * Update Strategy Method.
     *
     * @var string
     */
    public $method;

    /**
     * @var DateTime
     */
    public $startDate;

    /**
     * @var DateTime
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

    public static function fromArray(array $array = []) : UpdateEventCommand
    {
        $command = new self();

        $array = array_merge(get_object_vars($command), $array);

        $command->occurrence = $array['occurrence'];
        $command->method = $array['method'];
        $command->startDate = $array['startDate'];
        $command->endDate = $array['endDate'];
        $command->title = $array['title'];
        $command->repetitionDays = $array['repetitionDays'];

        return $command;
    }
}
