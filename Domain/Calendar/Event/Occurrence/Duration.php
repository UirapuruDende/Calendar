<?php
namespace Dende\Calendar\Domain\Calendar\Event\Occurrence;

use Dende\Calendar\Domain\Calendar\Event\Duration as EventDuration;

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
     * @param EventDuration|int $duration
     */
    public function __construct($duration)
    {
        if ($duration instanceof EventDuration) {
            $duration = $duration->minutes();
        }

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
