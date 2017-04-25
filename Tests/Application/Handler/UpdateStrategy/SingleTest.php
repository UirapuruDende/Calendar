<?php
namespace Dende\Calendar\Tests\Application\Handler;

use Carbon\Carbon;
use DateTime;
use Dende\Calendar\Application\Command\UpdateEventCommand;
use Dende\Calendar\Application\Handler\UpdateEventHandler;
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


}
