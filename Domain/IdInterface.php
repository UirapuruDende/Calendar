<?php
namespace Dende\Calendar\Domain;

interface IdInterface
{
    public static function create() : IdInterface;

    public function id() : string;

    public function __toString() : string;

    public function equals(IdInterface $id) : bool;
}
