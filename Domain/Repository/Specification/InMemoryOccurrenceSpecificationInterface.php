<?php
namespace Dende\Calendar\Domain\Repository\Specification;

use Dende\Calendar\Domain\Calendar\Event\Occurrence;

/**
 * Interface InMemoryOccurrenceSpecificationInterface
 * @package Gyman\Infrastructure\Persistence\InMemory
 */
interface InMemoryOccurrenceSpecificationInterface
{
    /**
     * @param Occurrence $occurrence
     * @return bool
     */
    public function specifies(Occurrence $occurrence);
}
