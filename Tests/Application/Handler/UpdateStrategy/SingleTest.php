<?php
namespace Dende\Calendar\Tests\Application\Handler;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\UpdateCommand;
use Dende\Calendar\Application\Handler\UpdateManager;
use Dende\Calendar\Application\Handler\UpdateStrategy\Single;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventId;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceDuration;
use Dende\Calendar\Domain\Calendar\Event\Occurrence\OccurrenceId;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryEventRepository;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit_Framework_TestCase;

/**
 * Class EventTest.
 */
final class SingleTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_updates_event()
    {
        $this->markTestSkipped();

        $base = Carbon::instance(new DateTime('last monday 12:00'));

        $event = new Event(
            EventId::create(),
            Calendar::create('test'),
            EventType::weekly(),
            $base->copy(),
            $base->copy()->addDays(6)->addHours(2),
            'some Title',
            Repetitions::workingDays()
        );

        /** @var Occurrence $occurrence */
        $occurrence = $event->occurrences()->get(2);

        $command = new UpdateCommand();

        $occurrenceRepository = new InMemoryOccurrenceRepository();
        $occurrenceRepository->insert($occurrence);

        $singleStrategy = new Single();

        $updateOccurrenceHandler->handle($command);
    }
}
