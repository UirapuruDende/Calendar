<?php
namespace Dende\Calendar\Domain\Calendar\Event;

/**
 * Class Duration
 * @package Gyman\Domain\Model
 */
final class Duration
{
    /**
     * @var int
     */
    private $minutes;

    /**
     * Duration constructor.
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
