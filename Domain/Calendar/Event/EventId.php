<?php
namespace Dende\Calendar\Domain\Calendar\Event;

/**
 * Class EventId.
 */
final class EventId
{
    /**
     * @var string
     */
    private $id;

    /**
     * EventId constructor.
     *
     * @param $id
     * @codeCoverageIgnore
     */
    public function __construct($id = null)
    {
        if (is_null($id)) {
            $id = uniqid('event_');
        }

        $this->id = $id;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function __toString()
    {
        return (string) $this->id();
    }
}
