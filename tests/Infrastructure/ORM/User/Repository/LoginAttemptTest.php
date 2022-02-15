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

namespace App\Tests\Infrastructure\ORM\User\Repository;

use App\Domain\User\Model;
use App\Domain\User\Repository as DomainRepo;
use App\Infrastructure\ORM\User\Repository as InfraRepo;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LoginAttemptTest extends TestCase
{
    use ProphecyTrait;
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
     * @var InfraRepo\LoginAttempt
     */
    private $infraRepo;

    public function setUp(): void
    {
        $this->registryProphecy      = $this->prophesize(RegistryInterface::class);
        $this->entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);

        $this->infraRepo = new InfraRepo\LoginAttempt($this->registryProphecy->reveal());
    }

    /**
     * Test if repo has good heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(DomainRepo\LoginAttempt::class, $this->infraRepo);
    }

    /**
     * Test getModelClass.
     *
     * @throws \ReflectionException
     */
    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\LoginAttempt::class,
            $this->invokeMethod($this->infraRepo, 'getModelClass')
        );
    }

    /**
     * Test findOneOrNullByIpAndEmail.
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testFindOneOrNullByIpAndEmail()
    {
        $attempt  = $this->prophesize(Model\LoginAttempt::class)->reveal();
        $email    = 'foo@email.com';
        $ip       = '192.168.1.1';

        // Query
        $queryProphecy = $this->prophesize(AbstractQuery::class);
        $queryProphecy->getOneOrNullResult()->shouldBeCalled()->willReturn($attempt);

        // QueryBuilder
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy
            ->select('o')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->from(Model\LoginAttempt::class, 'o')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->andWhere('o.email = :email')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->setParameter('email', $email)
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->andWhere('o.ip = :ip')
            ->shouldBeCalled()
            ->willReturn($queryBuilderProphecy)
        ;
        $queryBuilderProphecy
            ->setParameter('ip', $ip)
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
            $attempt,
            $this->infraRepo->findOneOrNullByIpAndEmail($ip, $email)
        );
    }
}
