<?php
namespace Dende\Calendar\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class RemoveEventCommand implements EventCommandInterface, UpdateEventCommandInterface
{
    /**
     * @var UuidInterface
     */
    protected $occurrenceId;

    /**
     * @var string
     */
    protected $method;

    public function __construct(UuidInterface $occurrenceId, string $method)
    {
        $this->occurrenceId = $occurrenceId;
        $this->method = $method;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function occurrenceId(): UuidInterface
    {
        return $this->occurrenceId;
    }
}
