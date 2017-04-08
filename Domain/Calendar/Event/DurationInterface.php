<?php
/**
 * Created by PhpStorm.
 * User: uirapuru
 * Date: 08/04/2017
 * Time: 22:32
 */

namespace Dende\Calendar\Domain\Calendar\Event;


interface DurationInterface
{
    public function minutes(): int;
}
