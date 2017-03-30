<?php
namespace Dende\Calendar\Application\Factory;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;

class OccurrenceFactory implements OccurrenceFactoryInterface
{
    public function createFromArray(array $array = []) : Occurrence
    {
        $template = [
            'id'        => OccurrenceId::create(),
            'event'     => null,
            'startDate' => new DateTime(),
            'duration'  => new OccurrenceDuration(),
        ];

        $array = array_merge($template, $array);

        return new Occurrence(
            $array['id'],
            $array['event'],
            $array['startDate'],
            $array['duration']
        );
    }
}
