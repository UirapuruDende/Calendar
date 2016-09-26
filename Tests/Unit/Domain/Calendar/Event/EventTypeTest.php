<?php
namespace Tests\Unit\Domain\Calendar\Event\EventTypeTest;

use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;

/**
 * Class EventTest.
 */
class EventTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Not allowed event type 'not_allowed', only [single, weekly] allowed
     */
    public function testConstructor()
    {
        new EventType('not_allowed');
    }
}
