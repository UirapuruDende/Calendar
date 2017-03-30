<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Domain\Calendar\Event;

interface EventFactoryInterface
{
    public function create() : Event;

    public function createFromArray(array $array = []) : Event;
}
