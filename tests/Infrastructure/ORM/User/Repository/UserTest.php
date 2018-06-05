<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Infrastructure\ORM\User\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\User\Model;
use App\Domain\User\Repository as DomainRepo;
use App\Infrastructure\ORM\User\Repository as InfraRepo;
use App\Tests\Utils\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var RegistryInterface
     */
    private $registryProphecy;

    /**
     * @var InfraRepo\User
     */
    private $infraRepo;

    public function setUp()
    {
        $this->registryProphecy = $this->prophesize(RegistryInterface::class);

        $this->infraRepo = new InfraRepo\User($this->registryProphecy->reveal());
    }

    /**
     * Test if repo has good heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(DomainRepo\User::class, $this->infraRepo);
        $this->assertInstanceOf(CRUDRepository::class, $this->infraRepo);
    }

    /**
     * Test getModelClass.
     *
     * @throws \ReflectionException
     */
    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\User::class,
            $this->invokeMethod($this->infraRepo, 'getModelClass')
        );
    }
}
