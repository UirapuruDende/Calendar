<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Symfony\Component\EventDispatcher\EventDispatcher;

trait SetDispatcherTrait
{
    /** @var EventDispatcher */
    protected $dispatcher;

    public function setEventDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
