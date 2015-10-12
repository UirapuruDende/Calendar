<?php
namespace Dende\Calendar\Domain\Repository;

/**
 * Interface EventRepositoryInterface
 * @package Gyman\Domain\Repository
 */
interface EventRepositoryInterface
{
    /**
     * @param $event
     * @return mixed
     */
    public function insert($event);

    public function update($event);

    public function findAll();
}
