<?php
namespace Dende\Calendar\Domain\Repository\Specification;

use Dende\Calendar\Domain\Calendar\Event;

/**
 * Interface InMemoryEventSpecificationInterface.
 */
interface InMemoryEventSpecificationInterface
{
    /**
     * @param Event $event
     *
     * @return bool
     */
    public function specifies(Event $event);
}
