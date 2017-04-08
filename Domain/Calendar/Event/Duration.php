<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use Exception;

/**
 * Class Duration.
 */
class Duration
{
    use CalculateTrait;

    /**
     * @var int
     */
    protected $minutes;

    /**
     * Duration constructor.
     *
     * @param int $minutes
     *
     * @throws Exception
     */
    public function __construct(int $minutes = 1)
    {
        if ($minutes < 1) {
            throw new Exception('Event duration has to be greater than 0');
        }

        $this->minutes = (int) $minutes;
    }

    public function minutes() : int
    {
        return $this->minutes;
    }
}
