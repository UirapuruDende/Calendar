<?php
namespace Dende\Calendar\Domain\Repository;
use Dende\Calendar\Domain\Calendar\Event;

/**
 * Interface EventRepositoryInterface
 * @package Dende\Calendar\Domain\Repository
 */
interface EventRepositoryInterface
{
    /**
     * @param $event
     * @return mixed
     */
    public function insert(Event $event);

    /**
     * @param Event $event
     * @return void
     */
    public function update(Event $event);

    /**
     * @param Event $event
     * @return void
     */
    public function remove(Event $event);

    /**
     * @return Event[]
     */
    public function findAll();
}
