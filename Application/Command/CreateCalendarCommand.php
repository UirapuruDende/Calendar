<?php
namespace Dende\Calendar\Application\Command;

use Dende\Calendar\Domain\Calendar\CalendarId;
use Dende\Calendar\Domain\IdInterface;

final class CreateCalendarCommand
{
    /**
     * @var IdInterface
     */
    public $calendarId;

    /**
     * @var string
     */
    public $title = '';

    public function __construct(IdInterface $calendarId = null, string $title = '')
    {
        $this->calendarId = $calendarId ?: CalendarId::create();
        $this->title = $title;
    }
}
