<?php
namespace Dende\Calendar\Tests\Application\Handler;

use Dende\Calendar\Application\Command\CreateCalendarCommand;
use Dende\Calendar\Application\Event\PostCreateCalendar;
use Dende\Calendar\Application\Factory\CalendarFactory;
use Dende\Calendar\Application\Handler\CreateCalendarHandler;
use Dende\Calendar\Domain\AbstractId;
use Dende\Calendar\Domain\Calendar;
use Dende\Calendar\Infrastructure\Persistence\InMemory\InMemoryCalendarRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class CreateCalendarHandlerTest extends TestCase
{
    public function testHandleCreateCommand()
    {
        $command = new CreateCalendarCommand(null, 'test-calendar');

        $eventDispatcher = $this->prophesize(EventDispatcher::class);
        $eventDispatcher->dispatch('post.create.calendar', Argument::type(PostCreateCalendar::class))->shouldBeCalled();

        $calendarRepository = new InMemoryCalendarRepository();

        $handler = new CreateCalendarHandler($calendarRepository, $eventDispatcher->reveal());

        $handler->handle($command);

        $this->assertCount(1, $calendarRepository->findAll());

        /** @var Calendar $createdCalendar */
        $createdCalendar = $calendarRepository->findAll()->first();

        $this->assertEquals('test-calendar', $createdCalendar->title());
        $this->assertCount(0, $createdCalendar->events());
    }
}
