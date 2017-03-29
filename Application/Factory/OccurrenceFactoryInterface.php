<?php
namespace Dende\Calendar\Application\Factory;

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
}
