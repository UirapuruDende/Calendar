<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Domain\Calendar\Event;

/**
 * Interface OccurrenceFactoryInterface.
 */
interface OccurrenceFactoryInterface
{
    /**
     * @param array $array
     *
     * @return mixed
     */
    public function createFromArray($array = []);

    /**
     * @param Event $event
     *
     * @return mixed
     */
    public function generateCollectionFromEvent(Event $event);
}
