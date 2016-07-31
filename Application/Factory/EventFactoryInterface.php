<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Application\Command\EventCommandInterface;

interface EventFactoryInterface
{
    public function create();

    public function createFromArray(array $array = []);

    public function createFromCommand(EventCommandInterface $command);
}
