<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
            ->getManager()
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
        $this->managerProphecy->flush()->shouldBeCalled();

        $this->registryProphecy->getManager()->shouldBeCalled()->willReturn($this->managerProphecy->reveal());

        $this->repository->insert($object);
    }

    public function testUpdate()
    {
        $object = new \StdClass();

        $this->managerProphecy->flush()->shouldBeCalled();

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
        $this->managerProphecy->flush()->shouldBeCalled();

        $this->registryProphecy->getManager()->shouldBeCalled()->willReturn($this->managerProphecy->reveal());

        $this->repository->remove($object);
    }

    public function testFindAll()
    {
        $data = [
            new \StdClass(),
            new \StdClass(),
        ];
        $order = [
            'foo' => 'bar',
        ];

        $this->objectRepositoryProphecy->findBy([], $order)->shouldBeCalled()->willReturn($data);
        $this->managerProphecy
            ->getRepository(DummyModelClass::class)
            ->shouldBeCalled()
            ->willReturn($this->objectRepositoryProphecy->reveal())
        ;

        $this->registryProphecy->getManager()->shouldBeCalled()->willReturn($this->managerProphecy->reveal());

        $this->repository->findAll($order);
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
