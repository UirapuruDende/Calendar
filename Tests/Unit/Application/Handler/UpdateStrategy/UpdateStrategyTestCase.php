<?php
namespace Dende\Calendar\Tests\Unit\Application\Handler\UpdateStrategy;

use Dende\Calendar\Application\Handler\UpdateEventHandler;
use Dende\Calendar\Application\Handler\UpdateStrategy\NextInclusive;
use Dende\Calendar\Application\Repository\EventRepositoryInterface;
use Dende\Calendar\Application\Repository\OccurrenceRepositoryInterface;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use PHPUnit_Framework_TestCase;

class UpdateStrategyTestCase extends PHPUnit_Framework_TestCase
{
    /** @var Event */
    protected $event;

    /** @var  EventRepositoryInterface */
    protected $eventRepository;

    /** @var  OccurrenceRepositoryInterface|InMemoryOccurrenceRepository */
    protected $occurrenceRepository;

    /** @var  UpdateEventHandler */
    protected $updateEventHandler;

    public function setUp()
    {
        $this->eventRepository = new InMemoryEventRepository();
        $this->occurrenceRepository = new InMemoryOccurrenceRepository();

        $this->updateEventHandler = new UpdateEventHandler($this->eventRepository, $this->occurrenceRepository);
        $this->updateEventHandler->addStrategy('nextinclusive', new NextInclusive());
    }

    public function tearDown()
    {
        $this->eventRepository = null;
        $this->occurrenceRepository = null;
        $this->event = null;
        $this->updateEventHandler = null;
    }
}
