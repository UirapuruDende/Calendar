<?php
namespace Dende\Calendar\Application\Factory;

use Dende\Calendar\Application\Generator\IdGeneratorInterface;
use Dende\Calendar\Domain\Calendar;

/**
 * Class CalendarFactory
 * @package Gyman\Domain
 * @todo change 'title' to name, because calendar has 'name' field
 */
class CalendarFactory
{
    /**
     * @var IdGeneratorInterface
     */
    private $idGenerator;

    /**
     * CalendarFactory constructor.
     * @param $idGenerator
     */
    public function __construct(IdGeneratorInterface $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param $params
     * @return Calendar
     */
    public function createFromArray($array)
    {
        $template = [
            'id'                     => $this->idGenerator->generateId(),
            'title'                  => '',
        ];

        $array = array_merge($template, $array);

        return new Calendar(
            $array['id'],
            $array['title']
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
