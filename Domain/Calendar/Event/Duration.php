<?php
namespace Dende\Calendar\Domain\Calendar\Event;

use Exception;

/**
 * Class Duration.
 */
class Duration implements DurationInterface
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

    public function equals(DurationInterface $duration): bool
    {
        return $this->minutes === $duration->minutes();
    }
}
