<?php
namespace Dende\Calendar\Domain\Calendar\Event\Occurrence;

use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\CalculateDurationTrait;
use Dende\Calendar\Domain\Calendar\Event\DurationInterface;
use Exception;

/**
 * Class Duration.
 */
class OccurrenceDuration implements DurationInterface
{
    use CalculateDurationTrait;

    /**
     * @var int
     */
    protected $minutes = 1;

    /**
     * Duration constructor.
     *
     * @param int $minutes
     *
     * @throws Exception
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

    public function equals(DurationInterface $duration): bool
    {
        return $this->minutes === $duration->minutes();
    }

    public static function fromEvent(Event $event) : self
    {
        return new self($event->duration()->minutes());
    }
}
