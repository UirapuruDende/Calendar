<?php
namespace Dende\Calendar\Domain\Calendar\Event\Occurrence;

use Exception;

/**
 * Class Duration.
 */
class OccurrenceDuration
{
    /**
     * @var int
     */
    protected $minutes = 1;

    /**
     * Duration constructor.
     *
     * @param int $minutes
     */
    public function __construct(int $minutes = 1)
    {
        if ($minutes <= 0) {
            throw new Exception('Occurrence duration has to be greater than 0');
        }

        $this->minutes = (int) $minutes;
    }

    /**
     * @return int
     */
    public function minutes() : int
    {
        return $this->minutes;
    }
}
