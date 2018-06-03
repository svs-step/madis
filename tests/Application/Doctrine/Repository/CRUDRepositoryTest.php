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

namespace App\Tests\Application\Doctrine\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Application\DDD\Repository\RepositoryInterface;
use App\Application\Doctrine\Repository\CRUDRepository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CRUDRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var RegistryInterface
     */
    private $registryProphecy;

    /**
     * @var EntityManagerInterface
     */
    private $managerProphecy;

    /**
     * @var ObjectRepository
     */
    private $objectRepositoryProphecy;

    /**
     * @var CRUDRepository
     */
    private $repository;

    public function setUp()
    {
        $this->registryProphecy         = $this->prophesize(RegistryInterface::class);
        $this->managerProphecy          = $this->prophesize(EntityManagerInterface::class);
        $this->objectRepositoryProphecy = $this->prophesize(ObjectRepository::class);

        $this->repository = new DummyRepository(
            $this->registryProphecy->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CRUDRepository::class, $this->repository);
        $this->assertInstanceOf(CRUDRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(RepositoryInterface::class, $this->repository);
    }

    public function testCreateQueryBuilder()
    {
        // Registry
        $this->registryProphecy
            ->getEntityManager()
            ->shouldBeCalled()
            ->willReturn($this->managerProphecy->reveal())
        ;

        // QueryBuilder
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy
            ->select('o')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;

        $queryBuilderProphecy
            ->from(DummyModelClass::class, 'o')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;

        // Manager
        $this->managerProphecy
            ->createQueryBuilder()
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy->reveal())
        ;

        $this->assertEquals(
            $queryBuilderProphecy->reveal(),
            $this->invokeMethod($this->repository, 'createQueryBuilder')
        );
    }

    public function testInsert()
    {
        $object = new \StdClass();

        $this->managerProphecy->persist($object)->shouldBeCalled();
        $this->managerProphecy->flush($object)->shouldBeCalled();

        $this->registryProphecy->getManager()->shouldBeCalled()->willReturn($this->managerProphecy->reveal());

        $this->repository->insert($object);
    }

    public function testUpdate()
    {
        $object = new \StdClass();

        $this->managerProphecy->flush($object)->shouldBeCalled();

        $this->registryProphecy->getManager()->shouldBeCalled()->willReturn($this->managerProphecy->reveal());

        $this->repository->update($object);
    }

    public function testCreate()
    {
        $this->assertInstanceOf(DummyModelClass::class, $this->repository->create());

        $this->repository->create();
    }

    public function testRemove()
    {
        $object = new \StdClass();

        $this->managerProphecy->remove($object)->shouldBeCalled();
        $this->managerProphecy->flush($object)->shouldBeCalled();

        $this->registryProphecy->getManager()->shouldBeCalled()->willReturn($this->managerProphecy->reveal());

        $this->repository->remove($object);
    }

    public function testFindAll()
    {
        $data = [
            new \StdClass(),
            new \StdClass(),
        ];

        $this->objectRepositoryProphecy->findAll()->shouldBeCalled()->willReturn($data);
        $this->managerProphecy
            ->getRepository(DummyModelClass::class)
            ->shouldBeCalled()
            ->willReturn($this->objectRepositoryProphecy->reveal())
        ;

        $this->registryProphecy->getManager()->shouldBeCalled()->willReturn($this->managerProphecy->reveal());

        $this->repository->findAll();
    }

    public function testFindOneById()
    {
        $idToCall            = '1';
        $modelObjectToReturn = 'Foo1';

        $this->objectRepositoryProphecy->find($idToCall)->shouldBeCalled()->willReturn($modelObjectToReturn);
        $this->managerProphecy
            ->getRepository(DummyModelClass::class)
            ->shouldBeCalled()
            ->willReturn($this->objectRepositoryProphecy->reveal())
        ;

        $this->registryProphecy->getManager()->shouldBeCalled()->willReturn($this->managerProphecy->reveal());

        $this->assertEquals(
            $modelObjectToReturn,
            $this->repository->findOneById($idToCall)
        );
    }
}

class DummyRepository extends CRUDRepository
{
    protected function getModelClass(): string
    {
        return DummyModelClass::class;
    }
}

class DummyModelClass
{
    public $id;
}
