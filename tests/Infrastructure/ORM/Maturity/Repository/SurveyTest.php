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

namespace App\Tests\Infrastructure\ORM\Maturity\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository as DomainRepo;
use App\Domain\User\Model\Collectivity;
use App\Infrastructure\ORM\Maturity\Repository as InfraRepo;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SurveyTest extends TestCase
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
     * @var InfraRepo\Survey
     */
    private $infraRepo;

    public function setUp()
    {
        $this->registryProphecy      = $this->prophesize(RegistryInterface::class);
        $this->entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);

        $this->infraRepo = new InfraRepo\Survey($this->registryProphecy->reveal());
    }

    /**
     * Test if repo has good heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(DomainRepo\Survey::class, $this->infraRepo);
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
            Model\Survey::class,
            $this->invokeMethod($this->infraRepo, 'getModelClass')
        );
    }

    /**
     * Test findAllByCollectivity.
     */
    public function testFindAllByCollectivity()
    {
        $collectivity = new Collectivity();
        $orderKey     = 'key';
        $orderDir     = 'asc';
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
            ->from(Model\Survey::class, 'o')
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
            ->addOrderBy("o.{$orderKey}", $orderDir)
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
            $this->infraRepo->findAllByCollectivity($collectivity, [$orderKey => $orderDir])
        );
    }

    /**
     * Test findPreviousById.
     */
    public function testFindPreviousById()
    {
        $id      = 'uuid';
        $limit   = 2;
        $results = ['dummyResult'];

        // Query
        $queryProphecy = $this->prophesize(AbstractQuery::class);
        $queryProphecy->getResult()->shouldBeCalled()->willReturn($results);

        // QueryBuilder
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);

        $queryBuilderProphecy->select('o')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->select('s')->shouldBeCalled()->willReturn($queryBuilderProphecy);

        $queryBuilderProphecy->from(Model\Survey::class, 'o')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->from(Model\Survey::class, 's')->shouldBeCalled()->willReturn($queryBuilderProphecy);

        $queryBuilderProphecy->andWhere('o.id = :id')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->andWhere('o.collectivity = s.collectivity')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->andWhere('o.createdAt > s.createdAt')->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->orderBy('s.createdAt', 'DESC')->shouldBeCalled()->willReturn($queryBuilderProphecy);

        $queryBuilderProphecy->setMaxResults($limit)->shouldBeCalled()->willReturn($queryBuilderProphecy);

        $queryBuilderProphecy->setParameter('id', $id)->shouldBeCalled()->willReturn($queryBuilderProphecy);
        $queryBuilderProphecy->getQuery()->shouldBeCalled()->willReturn($queryProphecy->reveal());

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
            $this->infraRepo->findPreviousById($id, $limit)
        );
    }
}
