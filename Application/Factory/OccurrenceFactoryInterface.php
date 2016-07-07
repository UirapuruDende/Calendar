<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Domain\Calendar\Event;

/**
 * Interface OccurrenceFactoryInterface
 * @package Dende\Calendar\Application\Factory
 */
interface OccurrenceFactoryInterface
{
    /**
     * @param array $array
     * @return mixed
     */
    public function createFromArray($array = []);

    /**
     * @param Event $event
     * @return mixed
     */
    public function generateCollectionFromEvent(Event $event);
}