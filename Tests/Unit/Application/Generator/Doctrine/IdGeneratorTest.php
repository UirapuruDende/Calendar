<?php
namespace Dende\Calendar\Tests\Unit\Application\Generator\Doctrine;

use Dende\Calendar\Application\Generator\Doctrine\IdGenerator;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Mockery as m;

class IdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $connection = m::mock(Connection::class);
        $connection->shouldReceive('getDatabasePlatform->getGuidExpression');
        $connection->shouldReceive('query->fetchColumn')->andReturn('test');

        $em = m::mock(EntityManager::class);
        $em->shouldReceive('getConnection')->andReturn($connection);

        $generator = new IdGenerator($em);

        $this->assertEquals('test', $generator->generateId());
    }
}
