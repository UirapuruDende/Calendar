<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Application\Handler\OccurrenceUpdateManager;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;

/**
 * Class CreateEventCommand.
 */
final class UpdateOccurrenceCommand implements EventCommandInterface, UpdateEventCommandInterface
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
    public $method = OccurrenceUpdateManager::MODE_SINGLE;

    /**
     * @var DateTime
     */
    public $startDate;

    /**
     * @var DateTime
     */
    public $endDate;

    /**
     * @var array
     */
    public $repetitions = [];

    /**
     * UpdateEventCommand constructor.
     *
     * @param string      $occurrenceId
     * @param string      $method
     * @param DateTime    $startDate
     * @param DateTime    $endDate
     * @param Repetitions $repetitions
     */
    public function __construct(string $occurrenceId, string $method, DateTime $startDate, DateTime $endDate, Repetitions $repetitions)
    {
        $this->occurrenceId = $occurrenceId;
        $this->method       = $method;
        $this->startDate    = $startDate;
        $this->endDate      = $endDate;
        $this->repetitions  = $repetitions;
    }

    public static function fromArray(array $array = []) : UpdateOccurrenceCommand
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
