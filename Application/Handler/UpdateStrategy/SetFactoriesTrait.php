<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;

trait SetFactoriesTrait
{
    /** @var EventFactoryInterface */
    private $eventFactory;

    /** @var OccurrenceFactoryInterface */
    private $occurrenceFactory;

    /**
     * @param EventFactory $eventFactory
     */
    public function setEventFactory(EventFactoryInterface $eventFactory)
    {
        $this->eventFactory = $eventFactory;
    }

    public function setOccurrenceFactory(OccurrenceFactoryInterface $occurrenceFactory)
    {
        $this->occurrenceFactory = $occurrenceFactory;
    }
}
