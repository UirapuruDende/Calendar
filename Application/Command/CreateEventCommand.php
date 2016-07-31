<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar;

/**
 * Class CreateEventCommand
 * @package Gyman\Domain\Command
 */
final class CreateEventCommand implements EventCommandInterface
{
    /**
     * @var string
     */
    public $type;

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
