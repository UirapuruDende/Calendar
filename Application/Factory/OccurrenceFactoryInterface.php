<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Domain\Calendar\Event\OccurrenceInterface;

interface OccurrenceFactoryInterface
{
    public function __construct();

    public function createFromArray(array $array = []) : OccurrenceInterface;
}
