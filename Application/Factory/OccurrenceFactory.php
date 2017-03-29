<?php
namespace Dende\Calendar\Application\Factory;

use DateTime;
use Dende\Calendar\Application\Generator\IdGeneratorInterface;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;

/**
 * Class OccurrenceFactory.
 */
class OccurrenceFactory implements OccurrenceFactoryInterface
{
    /**
     * @var IdGeneratorInterface
     */
    protected $idGenerator;

    /**
     * EventFactory constructor.
     *
     * @param IdGeneratorInterface $idGenerator
     */
    public function __construct(IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param array $array
     *
     * @return Occurrence
     */
    public function createFromArray($array = [])
    {
        $template = [
            'id'        => $this->idGenerator->generateId(),
            'startDate' => new DateTime('now'),
            'duration'  => 90,
        ];

        $array = array_merge($template, $array);

        return new Occurrence(
            $array['id'],
            $array['startDate'],
            new OccurrenceDuration($array['duration'])
        );
    }
}
