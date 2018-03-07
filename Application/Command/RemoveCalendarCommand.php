<?php
namespace Dende\Calendar\Application\Command;

use Ramsey\Uuid\UuidInterface;

final class RemoveCalendarCommand
{
    /**
     * @var UuidInterface
     */
    public $calendarId;

    public function __construct(UuidInterface $calendarId)
    {
        $this->calendarId = $calendarId;
    }

    public function calendarId(): UuidInterface
    {
        return $this->calendarId;
    }
}
