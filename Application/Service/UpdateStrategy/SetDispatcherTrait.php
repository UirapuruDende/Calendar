<?php
namespace Dende\Calendar\Application\Service\UpdateStrategy;

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
