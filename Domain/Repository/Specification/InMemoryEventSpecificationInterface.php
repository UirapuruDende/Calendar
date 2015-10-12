<?php
namespace Dende\Calendar\Domain\Repository\Specification;

use Dende\Calendar\Domain\Calendar\Event;

/**
 * Interface InMemoryEventSpecificationInterface
 * @package Gyman\Infrastructure\Persistence\InMemory
 */
interface InMemoryEventSpecificationInterface
{
    /**
     * @param Event $event
     * @return boolean
     */
    public function specifies(Event $event);
}
