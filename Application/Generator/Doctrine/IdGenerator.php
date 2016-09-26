<?php
namespace Dende\Calendar\Application\Generator\Doctrine;

use Dende\Calendar\Application\Generator\IdGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\UuidGenerator;

/**
 * Class IdGenerator.
 */
final class IdGenerator extends UuidGenerator implements IdGeneratorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * IdGenerator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return bool|mixed|string
     */
    public function generateId()
    {
        return parent::generate($this->em, null);
    }
}
