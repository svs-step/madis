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

namespace App\Tests\Infrastructure\ORM\Registry\Repository;

use App\Domain\Registry\Model;
use App\Domain\Registry\Repository as DomainRepo;
use App\Infrastructure\ORM\Registry\Repository as InfraRepo;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ViolationTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var ManagerRegistry
     */
    private $registryProphecy;

    /**
     * @var EntityManagerInterface
     */
    private $entityManagerProphecy;

    /**
     * @var InfraRepo\Proof
     */
    private $infraRepo;

    public function setUp(): void
    {
        $this->registryProphecy      = $this->prophesize(ManagerRegistry::class);
        $this->entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);

        $this->infraRepo = new InfraRepo\Violation($this->registryProphecy->reveal());
    }

    /**
     * Test if repo has good heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(DomainRepo\Violation::class, $this->infraRepo);
    }

    /**
     * Test getModelClass.
     *
     * @throws \ReflectionException
     */
    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Violation::class,
            $this->invokeMethod($this->infraRepo, 'getModelClass')
        );
    }
}
