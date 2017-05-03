<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;

/**
 * Class CreateEventCommand.
 */
final class UpdateOccurrenceCommand
{
    /**
     * Occurrence that was clicked to edit relating event.
     *
     * @var string
     */
    public $occurrenceId;

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
     * @param array $repetitions
     */
    public function __construct(string $occurrenceId, DateTime $startDate, DateTime $endDate, array $repetitions = [])
    {
        $this->occurrenceId = $occurrenceId;
        $this->startDate    = $startDate;
        $this->endDate      = $endDate;
        $this->repetitions  = $repetitions;
    }

    public static function fromArray(array $array = []) : UpdateOccurrenceCommand
    {
        return new self(
            $array['occurrenceId'],
            $array['startDate'],
            $array['endDate'],
            $array['title'],
            $array['repetitions']
        );
    }

    public function occurrenceId() : string
    {
        return $this->occurrenceId;
    }
}
