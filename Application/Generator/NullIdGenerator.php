<?php
namespace Dende\Calendar\Application\Generator;

/**
 * Class NullIdGenerator
 * @package Dende\Calendar\Application\Generator
 */
class NullIdGenerator implements IdGeneratorInterface
{
    /**
     * @return string
     */
    public function generateId()
    {
        return null;
    }
}