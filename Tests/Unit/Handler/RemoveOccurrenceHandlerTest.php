<?php
namespace Dende\Calendar\Tests\Unit\Handler;

use DateTime;
use Dende\Calendar\Application\Factory\EventFactory;
use Dende\Calendar\Application\Generator\NullIdGenerator;
use Dende\Calendar\Application\Handler\RemoveEventHandler;
use Dende\Calendar\Application\Handler\RemoveOccurrenceHandler;
use Dende\Calendar\Domain\Calendar\Event\Duration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * Class RemoveOccurrenceHandlerTest
 * @package Dende\Calendar\Application\Handler
 */
final class RemoveOccurrenceHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testRemove(){
        $this->markTestSkipped();

        $event = (new EventFactory(new NullIdGenerator()))->createFromArray([
            'id'                     => 'test-id',
        ]);

        $occurrence1 = new Occurrence(1, new DateTime("2015-10-14 12:00:00"), new Duration(90), $event);
        $occurrence2 = new Occurrence(2, new DateTime("2015-10-14 12:00:00"), new Duration(90), $event);
        $occurrence3 = new Occurrence(3, new DateTime("2015-10-14 12:00:00"), new Duration(90), $event);

        $occurrenceRepository = new InMemoryOccurrenceRepository();
        $occurrenceRepository->insert($occurrence1);
        $occurrenceRepository->insert($occurrence2);
        $occurrenceRepository->insert($occurrence3);

        $event->setOccurrences(new ArrayCollection([$occurrence1, $occurrence2, $occurrence3]));

        $eventRepositoryMock = m::mock(EventRepositoryInterface::class);
        $removeEventHandlerMock = new RemoveEventHandler($eventRepositoryMock);

        $handler = new RemoveOccurrenceHandler($removeEventHandlerMock, $eventRepositoryMock);
        $handler->remove($occurrence1);

        $this->assertCount(2, $occurrenceRepository->findAll());
        $this->assertCount(2, $event->occurrences());
    }


}