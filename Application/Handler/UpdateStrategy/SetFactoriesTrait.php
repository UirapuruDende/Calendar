<?php
namespace Dende\Calendar\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactory;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
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
     * @var OccurrenceFactoryInterface
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
     * @param OccurrenceFactoryInterface $occurrenceFactory
     */
    public function setOccurrenceFactory(OccurrenceFactoryInterface $occurrenceFactory)
    {
        $this->occurrenceFactory = $occurrenceFactory;
    }
}
