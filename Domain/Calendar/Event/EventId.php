<?php
namespace Dende\Calendar\Domain\Calendar\Event;

/**
 * Class EventId.
 */
class EventId
{
    /**
     * @var string
     */
    private $id;

    /**
     * EventId constructor.
     *
     * @param $id
     */
    public function __construct($id = null)
    {
        $this->id = null === $id ? uniqid() : $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }
}
