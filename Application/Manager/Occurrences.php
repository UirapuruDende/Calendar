<?php
namespace Dende\Calendar\Application\Manager;

use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

interface Occurrences
{
    public function occurrenceRepository() : OccurrenceRepositoryInterface;
    public function occurrenceFactory() : OccurrenceFactoryInterface;
}
