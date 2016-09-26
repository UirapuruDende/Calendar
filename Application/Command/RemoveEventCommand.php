<?php
namespace Dende\Calendar\Application\Command;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;

/**
 * Class RemoveEventCommand
 * @package Dende\Calendar\Application\Command
 */
final class RemoveEventCommand implements EventCommandInterface, UpdateEventCommandInterface
{
    use CommandConstructorTrait;

    /**
     * Occurrence that was clicked to edit relating event
     * @var Occurrence
     */
    public $occurrence;

    /**
     * Update Strategy Method
     * @var string
     */
    public $method;
}
