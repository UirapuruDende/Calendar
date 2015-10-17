<?php
namespace Dende\Calendar\Domain\Calendar\Event\Occurrence;

/**
 * Class OccurrenceId
 * @package Dende\Calendar\Domain\Calendar\Event\Occurrence
 */
final class OccurrenceId
{
    /**
     * @var string
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
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return (string) $this->id();
    }
}
