<?php
namespace Dende\Calendar\Application\Factory;

use DateTime;
use Dende\Calendar\Application\Generator\IdGeneratorInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class OccurrenceFactory
 * @package Gyman\Domain\Factory
 */
class OccurrenceFactory
{
    /**
     * @var IdGeneratorInterface
     */
    private $idGenerator;

    /**
     * EventFactory constructor.
     * @param IdGeneratorInterface $idGenerator
     */
    public function __construct(IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param array $array
     * @return Occurrence
     */
    public function createFromArray($array = [])
    {
        $template = [
            'id'             => $this->idGenerator->generateId(),
            'startDate'      => new DateTime('now'),
            'duration'       => new Duration(90),
            'event'          => null,
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
     * @return ArrayCollection|Occurrence[]
     */
    public function generateCollectionFromEvent(Event $event)
    {
        $dates = $event->calculateOccurrencesDates();

        $occurences = new ArrayCollection();

        foreach ($dates as $date) {
            $occurences->add($this->createFromArray([
                'startDate' => $date,
                'duration'  => $event->duration(),
                'event'     => $event,
            ]));
        }

        return $occurences;
    }
}
