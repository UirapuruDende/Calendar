<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Application\Handler\UpdateEventHandler;

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
    public $method = UpdateEventHandler::MODE_SINGLE;

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

    /**
     * UpdateEventCommand constructor.
     * @param string $occurrenceId
     * @param string $method
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string $title
     * @param array $repetitions
     */
    public function __construct(string $occurrenceId, string $method, DateTime $startDate, DateTime $endDate, string $title, array $repetitions)
    {
        $this->occurrenceId = $occurrenceId;
        $this->method = $method;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->title = $title;
        $this->repetitions = $repetitions;
    }

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

    public function occurrenceId() : string
    {
        return $this->occurrenceId;
    }
}
