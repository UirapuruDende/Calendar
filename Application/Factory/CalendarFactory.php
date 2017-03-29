<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Application\Generator\IdGeneratorInterface;
use Dende\Calendar\Domain\Calendar;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class CalendarFactory.
 *
 * @todo change 'title' to name, because calendar has 'name' field
 */
class CalendarFactory implements CalendarFactoryInterface
{
    /**
     * @var IdGeneratorInterface
     */
    protected $idGenerator;

    /**
     * CalendarFactory constructor.
     *
     * @param $idGenerator
     */
    public function __construct(IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param $params
     *
     * @return Calendar
     */
    public function createFromArray($array)
    {
        $template = [
            'id'     => $this->idGenerator->generateId(),
            'title'  => '',
            'events' => new ArrayCollection([]),
        ];

        $array = array_merge($template, $array);

        return new Calendar(
            $array['id'],
            $array['title'],
            $array['events']
        );
    }

    /**
     * @return Calendar
     */
    public function create()
    {
        return $this->createFromArray([]);
    }
}
