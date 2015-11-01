<?php
namespace Dende\Calendar\Application\Generator\InMemory;
use Dende\Calendar\Application\Generator\IdGeneratorInterface;

/**
 * Class IdGenerator
 * @package Dende\CalendarBundle\Service
 */
final class IdGenerator implements IdGeneratorInterface
{
    /**
     * @return bool|mixed|string
     */
    public function generateId()
    {
        return uniqid();
    }
}
