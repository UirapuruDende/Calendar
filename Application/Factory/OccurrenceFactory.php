<?php
namespace Dende\Calendar\Application\Factory;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;

class OccurrenceFactory implements OccurrenceFactoryInterface
{
    public function __construct()
    {
    }

    public function createFromArray(array $array = []) : Occurrence
    {
        $template = [
            'occurrenceId' => OccurrenceId::create(),
            'event'        => null,
            'startDate'    => new DateTime(),
            'duration'     => null,
        ];

        $array = array_merge($template, $array);

        return new Occurrence(
            $array['occurrenceId'],
            $array['event'],
            $array['startDate'],
            $array['duration']
        );
    }
}
