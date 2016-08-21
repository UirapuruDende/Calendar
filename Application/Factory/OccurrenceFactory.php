<?php
namespace Dende\Calendar\Application\Factory;

use DateTime;
use Dende\Calendar\Application\Generator\IdGeneratorInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\Duration as OccurrenceDuration;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class OccurrenceFactory
 * @package Gyman\Domain\Factory
 */
class OccurrenceFactory implements OccurrenceFactoryInterface
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
     * @param array $array
     * @return Occurrence
     */
    public function createFromArray($array = [])
    {
        $template = [
            'id'             => $this->idGenerator->generateId(),
            'startDate'      => new DateTime('now'),
            'duration'       => new OccurrenceDuration(90),
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

        $occurrences = new ArrayCollection();

        foreach ($dates as $date) {
            $occurrences->add($this->createFromArray([
                'startDate' => $date,
                'duration'  => new OccurrenceDuration($event->duration()->minutes()),
                'event'     => $event,
            ]));
        }

        return $occurrences;
    }
}
