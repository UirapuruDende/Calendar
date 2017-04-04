<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Domain\Calendar\Event;

interface EventFactoryInterface
{
    static public function create() : Event;

    static public function createFromArray(array $array = []) : Event;
}
