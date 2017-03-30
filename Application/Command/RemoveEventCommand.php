<?php
namespace Dende\Calendar\Application\Command;

use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;

/**
 * Class RemoveEventCommand.
 */
final class RemoveEventCommand implements EventCommandInterface, UpdateEventCommandInterface
{
    /**
     * Occurrence that was clicked to edit relating event.
     *
     * @var OccurrenceId
     */
    public $occurrenceId;

    /**
     * Update Strategy Method.
     *
     * @var string
     */
    public $method;

    public function method(): string
    {
        return $this->method;
    }
}
