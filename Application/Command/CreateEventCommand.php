<?php
namespace Dende\Calendar\Application\Command;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Domain\IdInterface;

/**
 * Class CreateEventCommand.
 */
final class CreateEventCommand implements EventCommandInterface
{
    /**
     * @var string
     */
    public $calendarId;

    /**
     * @var string
     */
    public $type;

    /**
     * @var DateTime|Carbon
     */
    public $startDate;

    /**
     * @var DateTime|Carbon
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
     * CreateEventCommand constructor.
     * @param string $calendarId
     * @param string $type
     * @param Carbon|DateTime $startDate
     * @param Carbon|DateTime $endDate
     * @param string $title
     * @param array $repetitions
     */
    public function __construct(IdInterface $calendarId, string $type, DateTime $startDate, DateTime $endDate, string $title, array $repetitions)
    {
        $this->calendarId = $calendarId;
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->title = $title;
        $this->repetitions = $repetitions;
    }

    public static function fromArray(array $array = []) : CreateEventCommand
    {
        return new self(
            $array['calendarId'],
            $array['type'],
            $array['startDate'],
            $array['endDate'],
            $array['title'],
            $array['repetitions']
        );
    }
}
