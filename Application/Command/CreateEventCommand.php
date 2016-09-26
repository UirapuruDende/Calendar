<?php
namespace Dende\Calendar\Application\Command;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Domain\Calendar;

/**
 * Class CreateEventCommand.
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
     * @var DateTime|Carbon
     */
    public $startDate;

    /**
     * @var DateTime|Carbon
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
