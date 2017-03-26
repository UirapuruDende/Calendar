<?php
namespace Dende\Calendar\Application\Manager;

use Dende\Calendar\Application\Factory\CalendarFactoryInterface;
use Dende\Calendar\Application\Factory\EventFactoryInterface;
use Dende\Calendar\Application\Factory\OccurrenceFactoryInterface;
use Dende\Calendar\Domain\Repository\CalendarRepositoryInterface;
use Dende\Calendar\Domain\Repository\EventRepositoryInterface;
use Dende\Calendar\Domain\Repository\OccurrenceRepositoryInterface;

class EntityManager implements Calendars, Events, Occurrences
{
    /** @var CalendarRepositoryInterface */
    protected $calendarRepository;

    /** @var CalendarFactoryInterface */
    protected $calendarFactory;

    /** @var EventRepositoryInterface */
    protected $eventRepository;

    /** @var EventFactoryInterface */
    protected $eventFactory;

    /** @var OccurrenceRepositoryInterface */
    protected $occurrenceRepository;

    /** @var OccurrenceFactoryInterface */
    protected $occurrenceFactory;

    /**
     * EntityManager constructor.
     *
     * @param CalendarRepositoryInterface   $calendarRepository
     * @param CalendarFactoryInterface      $calendarFactory
     * @param EventRepositoryInterface      $eventRepository
     * @param EventFactoryInterface         $eventFactory
     * @param OccurrenceRepositoryInterface $occurrenceRepository
     * @param OccurrenceFactoryInterface    $occurrenceFactory
     */
    public function __construct(CalendarRepositoryInterface $calendarRepository, CalendarFactoryInterface $calendarFactory, EventRepositoryInterface $eventRepository, EventFactoryInterface $eventFactory, OccurrenceRepositoryInterface $occurrenceRepository, OccurrenceFactoryInterface $occurrenceFactory)
    {
        $this->calendarRepository = $calendarRepository;
        $this->calendarFactory = $calendarFactory;
        $this->eventRepository = $eventRepository;
        $this->eventFactory = $eventFactory;
        $this->occurrenceRepository = $occurrenceRepository;
        $this->occurrenceFactory = $occurrenceFactory;
    }

    public function calendarRepository(): CalendarRepositoryInterface
    {
        return $this->calendarRepository;
    }

    public function calendarFactory(): CalendarFactoryInterface
    {
        return $this->calendarFactory;
    }

    public function eventRepository(): EventRepositoryInterface
    {
        return $this->eventRepository;
    }

    public function eventFactory(): EventFactoryInterface
    {
        return $this->eventFactory;
    }

    public function occurrenceRepository(): OccurrenceRepositoryInterface
    {
        return $this->occurrenceRepository;
    }

    public function occurrenceFactory(): OccurrenceFactoryInterface
    {
        return $this->occurrenceFactory;
    }
}
