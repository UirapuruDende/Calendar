<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;

/**
 * Class UpdateOccurrenceCommand.
 */
final class UpdateOccurrenceCommand
{
    /**
     * @var OccurrenceId
     */
    public $occurrenceId;

    /**
     * @var DateTime
     */
    public $startDate;

    /**
     * @var OccurrenceDuration
     */
    public $duration;
}
