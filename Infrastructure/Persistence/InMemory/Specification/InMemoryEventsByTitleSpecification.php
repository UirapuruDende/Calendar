<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory\Specification;

use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Repository\Specification\InMemoryEventSpecificationInterface;

/**
 * Class InMemoryEventsByTitleSpecification
 * @package Dende\Calendar\Infrastructure\Persistence\InMemory\Specification
 */
final class InMemoryEventsByTitleSpecification implements InMemoryEventSpecificationInterface
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * InMemoryEventsByTitleSpecification constructor.
     * @param string $title
     * @param Calendar $calendar
     */
    public function __construct($title, Calendar $calendar = null)
    {
        $this->title = $title;
        $this->calendar = $calendar;
    }

    /**
     * @param Event $event
     * @return boolean
     */
    public function specifies(Event $event)
    {
        if (!is_null($this->calendar) && !$event->calendar()->id() === $this->calendar->id()) {
            return false;
        }

        if ($event->title() === $this->title) {
            return true;
        }

        return false;
    }
}
