<?php
namespace Dende\Calendar\Application\Handler;

use Dende\Calendar\Application\Command\UpdateOccurrenceCommand;
use Dende\Calendar\Application\Service\OccurrenceUpdateManager;

class UpdateOccurrenceHandler
{

    /** @var OccurrenceUpdateManager */
    private $manager;

    /**
     * UpdateOccurrenceHandler constructor.
     * @param OccurrenceUpdateManager $manager
     */
    public function __construct(OccurrenceUpdateManager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(UpdateOccurrenceCommand $command)
    {
        die(var_dump($command));
    }
}
