<?php
namespace Dende\Calendar\Domain\Repository\Specification;

use Dende\Calendar\Domain\Calendar\Event\Occurrence;

/**
 * Interface InMemoryOccurrenceSpecificationInterface.
 */
interface InMemoryOccurrenceSpecificationInterface
{
    /**
     * @param Occurrence $occurrence
     *
     * @return bool
     */
    public function specifies(Occurrence $occurrence);
}
