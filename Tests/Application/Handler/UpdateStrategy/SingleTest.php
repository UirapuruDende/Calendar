<?php
namespace Dende\Calendar\Tests\Application\Handler;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\UpdateOccurrenceCommand;
use Dende\Calendar\Application\Handler\UpdateStrategy\Single;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Domain\Calendar\Event;
use Dende\Calendar\Domain\Calendar\Event\EventType;
use Dende\Calendar\Domain\Calendar\Event\Occurrence;
use Dende\Calendar\Domain\Calendar\Event\Repetitions;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryOccurrenceRepository;
use PHPUnit\Framework\TestCase;

/**
 * Class EventTest.
 */
final class SingleTest extends TestCase
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

        $occurrenceRepository = new InMemoryOccurrenceRepository();
        $occurrenceRepository->insert($occurrence);

        $command = new UpdateOccurrenceCommand();

        $singleStrategy = new Single();

        $singleStrategy->update($command);
    }
}
