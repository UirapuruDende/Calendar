<?php
namespace Dende\Calendar\Application\Repository;

use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Doctrine\Common\Collections\Collection;

/**
 * Interface EventRepositoryInterface.
 */
interface EventRepositoryInterface
{
    public function insert(Event $event);

    public function update(Event $event);

    public function remove(Event $event);

    /**
     * @return Event[]|Collection
     */
    public function findAll() : ArrayCollection;

    /**
     * @param Occurrence $occurrence
     *
     * @return Event|null
     */
    public function findOneByOccurrence(Occurrence $occurrence);
}
