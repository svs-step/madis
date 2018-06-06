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

namespace App\Tests\Infrastructure\ORM\Registry\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Admin\Model\Collectivity;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository as DomainRepo;
use App\Infrastructure\ORM\Registry\Repository as InfraRepo;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TreatmentTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var RegistryInterface
     */
    private $registryProphecy;

    /**
     * @var EntityManagerInterface
     */
    private $entityManagerProphecy;

    /**
     * @var InfraRepo\Treatment
     */
    private $infraRepo;

    public function setUp()
    {
        $this->registryProphecy      = $this->prophesize(RegistryInterface::class);
        $this->entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);

        $this->infraRepo = new InfraRepo\Treatment($this->registryProphecy->reveal());
    }

    /**
     * Test if repo has good heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(DomainRepo\Treatment::class, $this->infraRepo);
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
            Model\Treatment::class,
            $this->invokeMethod($this->infraRepo, 'getModelClass')
        );
    }

    /**
     * Test findAllByCollectivity.
     */
    public function testFindAllByCollectivity()
    {
        $collectivity = new Collectivity();
        $results      = ['dummyResult'];

        // Query
        $queryProphecy = $this->prophesize(AbstractQuery::class);
        $queryProphecy->getResult()->shouldBeCalled()->willReturn($results);

        // QueryBuilder
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy
            ->select('o')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->from(Model\Treatment::class, 'o')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->andWhere('o.collectivity = :collectivity')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->setParameter('collectivity', $collectivity)
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->getQuery()
            ->shouldBeCalled()
            ->willReturn($queryProphecy->reveal())
        ;

        // EntityManager
        $this->entityManagerProphecy
            ->createQueryBuilder()
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy->reveal());

        // Registry
        $this->registryProphecy
            ->getManager()
            ->shouldBeCalled()
            ->willReturn($this->entityManagerProphecy->reveal())
        ;

        $this->assertEquals(
            $results,
            $this->infraRepo->findAllByCollectivity($collectivity)
        );
    }
}
