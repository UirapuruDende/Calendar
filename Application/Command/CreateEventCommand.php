<?php
namespace Dende\Calendar\Application\Command;

use Carbon\Carbon;
use DateTime;
use Ramsey\Uuid\UuidInterface;

final class CreateEventCommand implements EventCommandInterface
{
    /**
     * @var UuidInterface
     */
    protected $calendarId;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var DateTime|Carbon
     */
    protected $startDate;

    /**
     * @var DateTime|Carbon
     */
    protected $endDate;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $repetitions = [];

    public function __construct(UuidInterface $calendarId = null, string $type, DateTime $startDate, DateTime $endDate, string $title, array $repetitions)
    {
        $this->calendarId  = $calendarId;
        $this->type        = $type;
        $this->startDate   = $startDate;
        $this->endDate     = $endDate;
        $this->title       = $title;
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

    public function calendarId(): UuidInterface
    {
        return $this->calendarId;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function startDate()
    {
        return $this->startDate;
    }

    public function endDate()
    {
        return $this->endDate;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function repetitions(): array
    {
        return $this->repetitions;
    }

}
