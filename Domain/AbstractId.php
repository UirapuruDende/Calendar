<?php
namespace Dende\Calendar\Domain;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AbstractId implements IdInterface
{
    /** @var string */
    protected $id;

    protected function __construct(UuidInterface $id)
    {
        $this->id = (string) $id;
    }

    public static function create(): IdInterface
    {
        return new self(Uuid::uuid4());
    }

    public function id(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function equals(IdInterface $id): bool
    {
        return $id->__toString() === $this->__toString();
    }
}
