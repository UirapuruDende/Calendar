<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;

/**
 * Class UpdateOccurrenceCommand.
 */
class UpdateOccurrenceCommand
{
    /**
     * @var string
     */
    protected $occurrenceId;

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * UpdateOccurrenceCommand constructor.
     *
     * @param string   $occurrenceId
     * @param DateTime $startDate
     * @param DateTime $endDate
     */
    public function __construct(string $occurrenceId, DateTime $startDate, DateTime $endDate)
    {
        $this->occurrenceId = $occurrenceId;
        $this->startDate    = $startDate;
        $this->endDate      = $endDate;
    }

    /**
     * @return OccurrenceId
     */
    public function occurrenceId(): string
    {
        return $this->occurrenceId;
    }

    /**
     * @return DateTime
     */
    public function startDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function endDate(): DateTime
    {
        return $this->endDate;
    }
}
