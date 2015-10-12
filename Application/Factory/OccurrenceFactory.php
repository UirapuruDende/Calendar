<?php
namespace Dende\Calendar\Application\Factory;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class OccurrenceFactory
 * @package Gyman\Domain\Factory
 */
final class OccurrenceFactory
{
    /**
     * @param array $array
     * @return Occurrence
     */
    public static function createFromArray($array = [])
    {
        $template = [
            'id'             => new OccurrenceId(),
            'startDate'      => new DateTime('now'),
            'duration'       => new Duration(90),
            'event'          => EventFactory::create(),
        ];

        $array = array_merge($template, $array);

        return new Occurrence(
            $array['id'],
            $array['startDate'],
            $array['duration'],
            $array['event']
        );
    }

    /**
     * @param Event $event
     * @return ArrayCollection
     */
    public static function generateCollectionFromEvent(Event $event)
    {
        $dates = $event->calculateOccurrencesDates();

        $occurences = new ArrayCollection();

        foreach ($dates as $date) {
            $occurences->add(self::createFromArray([
                'startDate' => $date,
                'duration'  => $event->duration(),
                'event'     => $event,
            ]));
        }

        return $occurences;
    }
}
