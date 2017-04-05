<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Domain\Calendar\Event;

interface EventFactoryInterface
{
    public static function create() : Event;

    public static function createFromArray(array $array = []) : Event;
}
