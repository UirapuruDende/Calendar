<?php
namespace Dende\Calendar\Infrastructure\Persistence\InMemory\Specification;

use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\Specification\InMemoryOccurrenceSpecificationInterface;

/**
 * Class InMemoryOccurrenceByEventSpecification.
 */
final class InMemoryOccurrenceByEventSpecification implements InMemoryOccurrenceSpecificationInterface
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @var bool
     */
    private $onlyUnmodified = false;

    /**
     * InMemoryOccurrenceByEventSpecification constructor.
     *
     * @param Event $event
     */
    public function __construct(Event $event, $onlyUnmodified = false)
    {
        $this->event          = $event;
        $this->onlyUnmodified = $onlyUnmodified;
    }

    /**
     * @param Occurrence $occurrence
     *
     * @return bool
     */
    public function specifies(Occurrence $occurrence)
    {
        if ($occurrence->isModified() && $this->onlyUnmodified) {
            return false;
        }

        return $occurrence->event()->id() === $this->event->id();
    }
}
