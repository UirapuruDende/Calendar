<?php
namespace Dende\Calendar\Application\Factory;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\CreateEventCommand;
use Dende\Calendar\Application\Generator\IdGeneratorInterface;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EventFactory
 * @package Gyman\Domain
 */
class EventFactory implements EventFactoryInterface
{
    /**
     * @var IdGeneratorInterface
     */
    protected $idGenerator;

    /**
     * EventFactory constructor.
     * @param IdGeneratorInterface $idGenerator
     */
    public function __construct(IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param $params
     * @return Event
     */
    public function createFromArray($array)
    {
        $template = [
            'id'                     => $this->idGenerator->generateId(),
            'title'                  => '',
            'repetitions'            => new Repetitions([]),
            'type'                   => new EventType(),
            'occurrences'            => new ArrayCollection(),
            'calendar'               => new Calendar(null, ''),
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
    public function create()
    {
        return self::createFromArray([]);
    }

    /**
     * @param CreateEventCommand $command
     * @return Event
     */
    public function createFromCommand(CreateEventCommand $command)
    {
        return static::createFromArray([
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
