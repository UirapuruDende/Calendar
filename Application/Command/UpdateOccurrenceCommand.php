<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;

/**
 * Class UpdateOccurrenceCommand.
 */
final class UpdateOccurrenceCommand
{
    use CommandConstructorTrait;

    /**
     * @var DateTime
     */
    public $startDate;

    /**
     * @var DateTime
     */
    public $endDate;

    /**
     * @var OccurrenceDuration
     */
    public $duration;

    /**
     * @var bool
     */
    public $modified;

    /**
     * @var Event
     */
    public $event;
}
