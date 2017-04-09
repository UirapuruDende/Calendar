<?php
namespace Dende\Calendar\Application\Factory;

use DateTime;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;

/**
 * Class EventFactory.
 */
class EventFactory implements EventFactoryInterface
{
    /**
     * @param array $array
     *
     * @return Event
     */
    public static function createFromArray(array $array = []) : Event
    {
        $template = [
            'eventId'     => EventId::create(),
            'title'       => '',
            'repetitions' => new Repetitions(),
            'type'        => new EventType(),
            'occurrences' => null,
            'calendar'    => null,
            'startDate'   => new DateTime('now'),
            'endDate'     => new DateTime('now'),
        ];

        $array = array_merge($template, $array);

        return new Event(
            $array['eventId'],
            $array['calendar'],
            $array['type'],
            $array['startDate'],
            $array['endDate'],
            $array['title'],
            $array['repetitions'],
            $array['occurrences']
        );
    }

    public static function create() : Event
    {
        return self::createFromArray([]);
    }
}
