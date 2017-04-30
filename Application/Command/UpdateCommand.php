<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Application\Handler\UpdateManager;

/**
 * Class CreateEventCommand.
 */
final class UpdateCommand implements EventCommandInterface, UpdateEventCommandInterface
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
    public $method = UpdateManager::MODE_SINGLE;

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
     *
     * @param string   $occurrenceId
     * @param string   $method
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string   $title
     * @param array    $repetitions
     */
    public function __construct(string $occurrenceId, string $method, DateTime $startDate, DateTime $endDate, string $title, array $repetitions)
    {
        $this->occurrenceId = $occurrenceId;
        $this->method       = $method;
        $this->startDate    = $startDate;
        $this->endDate      = $endDate;
        $this->title        = $title;
        $this->repetitions  = $repetitions;
    }

    public static function fromArray(array $array = []) : UpdateCommand
    {
        return new self(
            $array['occurrenceId'],
            $array['method'],
            $array['startDate'],
            $array['endDate'],
            $array['title'],
            $array['repetitions']
        );
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
