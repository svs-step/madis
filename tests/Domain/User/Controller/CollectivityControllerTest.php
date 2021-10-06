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

namespace App\Tests\Domain\User\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\Registry\Repository as RegistryRepository;
use App\Domain\User\Controller\CollectivityController;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class CollectivityControllerTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var Security|ObjectProphecy
     */
    private $security;

    /**
     * @var Repository\Collectivity|ObjectProphecy
     */
    private $repository;

    /**
     * @var CollectivityController
     */
    private $controller;

    public function setUp()
    {
        $this->security   = $this->prophesize(Security::class);
        $this->repository = $this->prophesize(Repository\Collectivity::class);

        $this->controller = new CollectivityController(
            $this->prophesize(EntityManagerInterface::class)->reveal(),
            $this->prophesize(TranslatorInterface::class)->reveal(),
            $this->repository->reveal(),
            $this->prophesize(Pdf::class)->reveal(),
            $this->prophesize(RouterInterface::class)->reveal(),
            $this->security->reveal(),
            $this->prophesize(RegistryRepository\Treatment::class)->reveal(),
            $this->prophesize(RegistryRepository\Contractor::class)->reveal(),
            $this->prophesize(RegistryRepository\Proof::class)->reveal(),
            $this->prophesize(RegistryRepository\Mesurement::class)->reveal(),
            $this->prophesize(Repository\User::class)->reveal(),
            $this->prophesize(UserProvider::class)->reveal(),
            $this->prophesize(AuthorizationCheckerInterface::class)->reveal(),
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(CRUDController::class, $this->controller);
    }

    public function testGetDomain()
    {
        $this->assertEquals(
            'user',
            $this->invokeMethod($this->controller, 'getDomain', [])
        );
    }

    public function testGetModel()
    {
        $this->assertEquals(
            'collectivity',
            $this->invokeMethod($this->controller, 'getModel', [])
        );
    }

    public function testGetModelClass()
    {
        $this->assertEquals(
            Model\Collectivity::class,
            $this->invokeMethod($this->controller, 'getModelClass', [])
        );
    }

    public function testGetFormType()
    {
        $this->assertEquals(
            CollectivityType::class,
            $this->invokeMethod($this->controller, 'getFormType', [])
        );
    }

    public function testItDeniedAccessOnNonReferedCollectivity()
    {
        $this->expectException(AccessDeniedException::class);

        $user         = $this->prophesize(Model\User::class);
        $collectivity = $this->prophesize(Model\Collectivity::class);

        $user->getRoles()->shouldBeCalled()->willReturn([UserRoleDictionary::ROLE_REFERENT]);
        $user->getCollectivitesReferees()->shouldBeCalled()->willReturn([$collectivity->reveal()]);
        $collectivity->getId()->shouldBeCalled()->willReturn(Uuid::uuid4());

        $this->security->getUser()->shouldBeCalled()->willReturn($user->reveal());

        $this->controller->showAction('foo');
    }
}
