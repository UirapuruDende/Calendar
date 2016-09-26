<?php
namespace Dende\Calendar\Domain\Repository;

use Dende\Calendar\Domain\Calendar\Event;

/**
 * Interface EventRepositoryInterface.
 */
interface EventRepositoryInterface
{
    /**
     * @param $event
     *
     * @return mixed
     */
    public function insert(Event $event);

    /**
     * @param Event $event
     */
    public function update(Event $event);

    /**
     * @param Event $event
     */
    public function remove(Event $event);

    /**
     * @return Event[]
     */
    public function findAll();
}
