<?php
namespace Dende\Calendar\Domain\Calendar\Event;

/**
 * Class Repetitions.
 */
class Repetitions
{
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;
    const SUNDAY    = 7;

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
    public function __construct(array $weekdays = [])
    {
        $this->weekdays = $weekdays;
    }

    public static function none() : Repetitions
    {
        return new self([]);
    }

    public static function daily() : Repetitions
    {
        return new self([
            self::MONDAY,
            self::TUESDAY,
            self::WEDNESDAY,
            self::THURSDAY,
            self::FRIDAY,
            self::SATURDAY,
            self::SUNDAY,
        ]);
    }

    public static function create(array $array = [])
    {
        return new self($array);
    }

    /**
     * @return array
     */
    public function getArray() : array
    {
        return $this->weekdays;
    }

    /**
     * @param int[] $repetitions
     *
     * @return bool
     */
    public function sameDays($repetitions) : bool
    {
        $a = $this->weekdays;
        $b = $repetitions;

        sort($a);
        sort($b);

        return $a == $b;
    }

    public static function workingDays() : Repetitions
    {
        return new self([
            self::MONDAY,
            self::TUESDAY,
            self::WEDNESDAY,
            self::THURSDAY,
            self::FRIDAY,
        ]);
    }

    public static function weekendDays() : Repetitions
    {
        return new self([
            self::SATURDAY,
            self::SUNDAY,
        ]);
    }
}
