<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Ramsey\Uuid\UuidInterface;

final class UpdateEventCommand
{
    /**
     * @var UuidInterface
     */
    protected $eventId;

    /**
     * @var UuidInterface
     */
    protected $occurrenceId;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
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

    public function __construct(UuidInterface $eventId, UuidInterface $occurrenceId, string $method, DateTime $startDate, DateTime $endDate, string $title, array $repetitions)
    {
        $this->eventId = $eventId;
        $this->occurrenceId = $occurrenceId;
        $this->method = $method;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->title = $title;
        $this->repetitions = $repetitions;
    }

    public function eventId(): UuidInterface
    {
        return $this->eventId;
    }

    public function occurrenceId(): UuidInterface
    {
        return $this->occurrenceId;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function startDate(): DateTime
    {
        return $this->startDate;
    }

    public function endDate(): DateTime
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
