<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;

trait SetFactoriesTrait
{
    /**
     * @param EventFactory $eventFactory
     */
    public function setEventFactory(EventFactoryInterface $eventFactory)
    {
        // TODO: Implement setEventFactory() method.
    }

    public function setOccurrenceFactory(OccurrenceFactoryInterface $occurrenceFactory)
    {

    }
}