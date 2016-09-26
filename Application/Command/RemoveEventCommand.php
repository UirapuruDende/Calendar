<?php
namespace Dende\Calendar\Application\Command;

use Dende\Calendar\Domain\Calendar\Event\Occurrence;

/**
 * Class RemoveEventCommand.
 */
final class RemoveEventCommand implements EventCommandInterface, UpdateEventCommandInterface
{
    use CommandConstructorTrait;

    /**
     * Occurrence that was clicked to edit relating event.
     *
     * @var Occurrence
     */
    public $occurrence;

    /**
     * Update Strategy Method.
     *
     * @var string
     */
    public $method;
}
