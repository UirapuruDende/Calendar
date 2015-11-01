<?php
namespace Dende\Calendar\Application\Generator;

/**
 * Interface IdGeneratorInterface
 * @package Dende\Calendar\Application\Generator
 */
interface IdGeneratorInterface
{
    /**
     * @return string
     */
    public function generateId();
}