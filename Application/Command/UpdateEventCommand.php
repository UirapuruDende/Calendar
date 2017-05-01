<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Application\Handler\UpdateManager;

/**
 * Class CreateEventCommand.
 */
final class UpdateEventCommand
{
    /**
     * @var string
     */
    public $eventId;

    /**
     * @var string
     */
    public $occurrenceId;

    /**
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

    /**
     * UpdateEventCommand constructor.
     *
     * @param string $eventId
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string $title
     * @param array $repetitions
     * @param string $method
     * @param string $occurrenceId
     */
    public function __construct(string $eventId, DateTime $startDate, DateTime $endDate, string $title, array $repetitions, string $method, string $occurrenceId)
    {
        $this->eventId = $eventId;
        $this->startDate    = $startDate;
        $this->endDate      = $endDate;
        $this->title        = $title;
        $this->repetitions  = $repetitions;
        $this->method = $method;
        $this->occurrenceId = $occurrenceId;
    }
}
