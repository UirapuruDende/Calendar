<?php
namespace Dende\Calendar\Application\Command;

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

    public function __construct(IdInterface $calendarId, string $title)
    {
        $this->calendarId = $calendarId;
        $this->title = $title;
    }
}
