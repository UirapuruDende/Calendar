<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;

/**
 * Class CreateEventCommand.
 */
final class UpdateEventCommand implements EventCommandInterface, UpdateEventCommandInterface
{
    /**
     * Occurrence that was clicked to edit relating event.
     *
     * @var string
     */
    public $occurrenceId;

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
    public $repetitions = [];

    public static function fromArray(array $array = []) : UpdateEventCommand
    {
        $command = new self();

        $command->occurrenceId = $array['occurrenceId'];
        $command->method       = $array['method'];
        $command->startDate    = $array['startDate'];
        $command->endDate      = $array['endDate'];
        $command->title        = $array['title'];
        $command->repetitions  = $array['repetitions'];

        return $command;
    }

    public function method(): string
    {
        return $this->method;
    }
}
