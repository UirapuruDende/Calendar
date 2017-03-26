<?php
namespace Tests\Unit\Domain\Calendar\Event;

use DateTime;
use Dende\Calendar\Domain\Calendar\Event\Duration;

class DurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider calculateDataProvider
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param $expectedResult
     */
    public function testCalculate(DateTime $startDate, DateTime $endDate, int $expectedResult)
    {
        $this->assertEquals($expectedResult, Duration::calculate($startDate, $endDate)->minutes());
    }

    public function calculateDataProvider() : array
    {
        return [
            'same day' => [
                'startDate'      => new DateTime('2009-10-14 12:30'),
                'endDate'        => new DateTime('2009-10-14 12:35'),
                'expectedResult' => 5,
            ],
            'different day' => [
                'startDate'      => new DateTime('2009-10-14 12:30'),
                'endDate'        => new DateTime('2009-10-15 12:35'),
                'expectedResult' => 5,
            ],
            'inverted dates' => [
                'startDate'      => new DateTime('2009-10-15 12:30'),
                'endDate'        => new DateTime('2009-10-14 12:35'),
                'expectedResult' => 5,
            ],
            'several hours' => [
                'startDate'      => new DateTime('2009-10-14 10:00'),
                'endDate'        => new DateTime('2009-10-14 12:35'),
                'expectedResult' => 155,
            ],
            'whole day' => [
                'startDate'      => new DateTime('2009-10-14 00:00:00'),
                'endDate'        => new DateTime('2009-10-14 23:59:50'),
                'expectedResult' => 24 * 60 - 1,
            ],
        ];
    }
}
