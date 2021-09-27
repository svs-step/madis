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

use App\Application\Controller\ControllerHelper;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\User\Controller\ProfileController;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ProfileControllerTest extends TestCase
{
    use ReflectionTrait;
    use ControllerTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ControllerHelper
     */
    private $controllerHelperProphecy;

    /**
     * @var RequestStack
     */
    private $requestStackProphecy;

    /**
     * @var UserProvider
     */
    private $userProviderProphecy;

    /**
     * @var Repository\Collectivity
     */
    private $collectivityRepositoryProphecy;

    /**
     * @var Repository\User
     */
    private $userRepositoryProphecy;

    /**
     * @var ProfileController
     */
    private $controller;

    public function setUp()
    {
        $this->entityManager                  = $this->prophesize(EntityManagerInterface::class);
        $this->controllerHelperProphecy       = $this->prophesize(ControllerHelper::class);
        $this->requestStackProphecy           = $this->prophesize(RequestStack::class);
        $this->userProviderProphecy           = $this->prophesize(UserProvider::class);
        $this->collectivityRepositoryProphecy = $this->prophesize(Repository\Collectivity::class);
        $this->userRepositoryProphecy         = $this->prophesize(Repository\User::class);

        $this->controller = new ProfileController(
            $this->entityManager->reveal(),
            $this->controllerHelperProphecy->reveal(),
            $this->requestStackProphecy->reveal(),
            $this->userProviderProphecy->reveal(),
            $this->collectivityRepositoryProphecy->reveal(),
            $this->userRepositoryProphecy->reveal()
        );
    }

    /**
     * Test collectivityShowAction.
     */
    public function testCollectivityShowAction()
    {
        $collectivity = $this->prophesize(Model\Collectivity::class)->reveal();
        $userProphecy = $this->prophesize(Model\User::class);
        $response     = new Response();

        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy
            ->getAuthenticatedUser()
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;

        $this->controllerHelperProphecy
            ->render('User/Profile/collectivity_show.html.twig', ['object' => $collectivity])
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals($response, $this->controller->collectivityShowAction());
    }
}
