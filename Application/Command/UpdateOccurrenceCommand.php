<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Ramsey\Uuid\UuidInterface;

final class UpdateOccurrenceCommand
{
    /** @var UuidInterface */
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
     * @var array
     */
    protected $repetitions = [];

    public function __construct(UuidInterface $occurrenceId, DateTime $startDate, DateTime $endDate, array $repetitions)
    {
        $this->occurrenceId = $occurrenceId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->repetitions = $repetitions;
    }

    public static function fromArray(array $array = []) : UpdateOccurrenceCommand
    {
        return new self(
            $array['occurrenceId'],
            $array['startDate'],
            $array['endDate'],
            $array['repetitions']
        );
    }

    public function occurrenceId(): UuidInterface
    {
        return $this->occurrenceId;
    }

    public function startDate(): DateTime
    {
        return $this->startDate;
    }

    public function endDate(): DateTime
    {
        return $this->endDate;
    }

    public function repetitions(): array
    {
        return $this->repetitions;
    }
}
