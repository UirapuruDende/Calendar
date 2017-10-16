<?php
namespace Dende\Calendar\Domain;

use Ramsey\Uuid\UuidInterface;

interface IdInterface extends UuidInterface
{
    public static function create() : IdInterface;

    public function id() : string;

    public function __toString() : string;

    public function equals($id) : bool;
}
