<?php
namespace Dende\Calendar\Domain\Calendar\Event;

/**
 * Class Duration.
 */
class Duration
{
    /**
     * @var int
     */
    protected $minutes;

    /**
     * Duration constructor.
     *
     * @param int $minutes
     */
    public function __construct($minutes)
    {
        $this->minutes = intval($minutes);
    }

    /**
     * @return int
     */
    public function minutes()
    {
        return $this->minutes;
    }
}
