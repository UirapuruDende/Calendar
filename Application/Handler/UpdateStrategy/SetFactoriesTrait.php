<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Domain\Calendar\Event;

/**
 * Class SetRepositoriesTrait
 * @package Dende\Calendar\Application\Handler\UpdateStrategy
 */
trait SetFactoriesTrait
{
    /**
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @var OccurrenceFactory
     */
    private $occurrenceFactory;

    /**
     * @param EventFactory $eventFactory
     */
    public function setEventFactory(EventFactory $eventFactory)
    {
        $this->eventFactory = $eventFactory;
    }

    /**
     * @param OccurrenceFactory $occurrenceFactory
     */
    public function setOccurrenceFactory(OccurrenceFactory $occurrenceFactory)
    {
        $this->occurrenceFactory = $occurrenceFactory;
    }
}
