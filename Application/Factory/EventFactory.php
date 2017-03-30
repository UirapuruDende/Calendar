<?php
namespace Dende\Calendar\Application\Factory;

use DateTime;
use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Command\EventCommandInterface;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;

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
    public function createFromArray(array $array = []) : Event
    {
        $template = [
            'eventId'     => EventId::create(),
            'title'       => '',
            'repetitions' => new Repetitions([]),
            'type'        => new EventType(),
            'occurrences' => new ArrayCollection([]),
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

    public function create() : Event
    {
        return self::createFromArray([]);
    }

    /**
     * @param CreateEventCommand|UpdateEventCommand $command
     */
    public function createFromCommand(EventCommandInterface $command) : Event
    {
        return static::createFromArray([
            'title'       => $command->title,
            'calendar'    => $command->calendar,
            'repetitions' => new Repetitions($command->repetitions),
            'type'        => new EventType($command->type),
            'startDate'   => $command->startDate,
            'endDate'     => $command->endDate,
        ]);
    }
}
