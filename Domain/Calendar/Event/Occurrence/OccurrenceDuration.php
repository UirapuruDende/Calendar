<?php
namespace Dende\Calendar\Domain\Calendar\Event\Occurrence;

/**
 * Class Duration.
 */
class OccurrenceDuration
{
    /**
     * @var int
     */
    protected $minutes;

    /**
     * Duration constructor.
     *
     * @param int $duration
     */
    public function __construct($duration = 0)
    {
        $this->minutes = intval($duration);
    }

    /**
     * @return int
     */
    public function minutes()
    {
        return $this->minutes;
    }
}
