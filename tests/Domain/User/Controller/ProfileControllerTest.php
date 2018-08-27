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

namespace App\Tests\Domain\User\Controller;

use App\Application\Controller\ControllerHelper;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\User\Controller\ProfileController;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use App\Tests\Utils\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ProfileControllerTest extends TestCase
{
    use ReflectionTrait;

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
        $this->controllerHelperProphecy       = $this->prophesize(ControllerHelper::class);
        $this->requestStackProphecy           = $this->prophesize(RequestStack::class);
        $this->userProviderProphecy           = $this->prophesize(UserProvider::class);
        $this->collectivityRepositoryProphecy = $this->prophesize(Repository\Collectivity::class);
        $this->userRepositoryProphecy         = $this->prophesize(Repository\User::class);

        $this->controller = new ProfileController(
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
