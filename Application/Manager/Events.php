<?php
namespace Dende\Calendar\Application\Manager;

use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;

interface Events
{
    public function eventRepository() : EventRepositoryInterface;
    public function eventFactory() : EventFactoryInterface;
}
