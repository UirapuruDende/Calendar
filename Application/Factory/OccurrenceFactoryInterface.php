<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Domain\Calendar\Event\Occurrence;

interface OccurrenceFactoryInterface
{
    public function __construct();

    public function createFromArray(array $array = []) : Occurrence;
}
