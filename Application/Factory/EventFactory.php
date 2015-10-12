<?php
namespace Dende\Calendar\Application\Factory;

use DateTime;
use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EventFactory
 * @package Gyman\Domain
 */
final class EventFactory implements EventFactoryInterface
{
    /**
     * @param $params
     * @return Event
     */
    public static function createFromArray($array)
    {
        $template = [
            'id'                     => new EventId(null),
            'title'                  => '',
            'repetitions'            => new Repetitions([]),
            'type'                   => new EventType(),
            'occurrences'            => new ArrayCollection(),
            'calendar'               => new Calendar(new CalendarId(null), ''),
            'duration'               => new Duration(0),
            'startDate'              => new DateTime('now'),
            'endDate'                => new DateTime('now'),
        ];

        $array = array_merge($template, $array);

        return new Event(
            $array['id'],
            $array['calendar'],
            $array['type'],
            $array['startDate'],
            $array['endDate'],
            $array['title'],
            $array['repetitions'],
            $array['duration']
        );
    }

    /**
     * @return Event
     */
    public static function create()
    {
        return self::createFromArray([]);
    }

    /**
     * @param CreateEventCommand $command
     * @return Event
     */
    public static function createFromCommand(CreateEventCommand $command)
    {
        return self::createFromArray([
            'title'           => $command->title,
            'calendar'        => $command->calendar,
            'repetitions'     => new Repetitions($command->repetitionDays),
            'type'            => new EventType($command->type),
            'startDate'       => $command->startDate,
            'endDate'         => $command->endDate,
            'duration'        => new Duration($command->duration),
        ]);
    }
}
