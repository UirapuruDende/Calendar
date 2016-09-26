<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;

/**
 * Class CreateEventCommand
 * @package Gyman\Domain\Command
 */
final class UpdateEventCommand implements  EventCommandInterface, UpdateEventCommandInterface
{
    use CommandConstructorTrait;

    /**
     * Occurrence that was clicked to edit relating event
     * @var Occurrence
     */
    public $occurrence;

    /**
     * @var string
     */
    public $type;

    /**
     * Update Strategy Method
     * @var string
     */
    public $method;

    /**
     * @var Calendar
     */
    public $calendar;

    /**
     * @var DateTime
     */
    public $startDate;

    /**
     * @var DateTime
     */
    public $endDate;

    /**
     * @var int
     */
    public $duration = 90;

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var array
     */
    public $repetitionDays = [];
}
