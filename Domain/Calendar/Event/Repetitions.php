<?php
namespace Dende\Calendar\Domain\Calendar\Event;

/**
 * Class Repetitions.
 */
class Repetitions
{
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    /**
     * @var array
     */
    public static $availableWeekdays = [
        self::MONDAY    => 'monday',
        self::TUESDAY   => 'tuesday',
        self::WEDNESDAY => 'wednesday',
        self::THURSDAY  => 'thursday',
        self::FRIDAY    => 'friday',
        self::SATURDAY  => 'saturday',
        self::SUNDAY    => 'sunday',
    ];

    /**
     * @var int[]
     */
    private $weekdays = [];

    /**
     * Repetitions constructor.
     *
     * @param array $weekdays
     */
    public function __construct(array $weekdays)
    {
        $this->weekdays = $weekdays;
    }

    /**
     * @return array
     */
    public function weekdays()
    {
        return $this->weekdays;
    }

    /**
     * @param int[] $repetitions
     *
     * @return bool
     */
    public function sameDays($repetitions)
    {
        $a = $this->weekdays;
        $b = $repetitions;

        sort($a);
        sort($b);

        return $a == $b;
    }
}
