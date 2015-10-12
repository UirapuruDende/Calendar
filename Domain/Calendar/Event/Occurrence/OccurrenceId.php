<?php
namespace Dende\Calendar\Domain\Calendar\Event\Occurrence;

/**
 * Class OccurrenceId
 * @package Dende\Calendar\Domain\Calendar\Event\Occurrence
 */
final class OccurrenceId
{
    /**
     * @var
     */
    private $id;

    /**
     * OccurrenceId constructor.
     * @param $id
     */
    public function __construct($id = null)
    {
        if (is_null($id)) {
            $id = uniqid('occurrence_');
        }

        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }
}
