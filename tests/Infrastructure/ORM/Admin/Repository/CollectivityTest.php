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

namespace App\Tests\Infrastructure\ORM\Admin\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Admin\Model;
use App\Domain\Admin\Repository as DomainRepo;
use App\Infrastructure\ORM\Admin\Repository as InfraRepo;
use App\Tests\Utils\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CollectivityTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var RegistryInterface
     */
    private $registryProphecy;

    /**
     * @var InfraRepo\Collectivity
     */
    private $infraRepo;

    public function setUp()
    {
        $this->registryProphecy = $this->prophesize(RegistryInterface::class);

        $this->infraRepo = new InfraRepo\Collectivity($this->registryProphecy->reveal());
    }

    /**
     * Test if repo has good heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(DomainRepo\Collectivity::class, $this->infraRepo);
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
            Model\Collectivity::class,
            $this->invokeMethod($this->infraRepo, 'getModelClass')
        );
    }
}
