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

use App\Domain\User\Controller\SecurityController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityControllerTest extends TestCase
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtilsProphecy;

    /**
     * @var SecurityController
     */
    private $controller;

    public function setUp()
    {
        $this->authenticationUtilsProphecy = $this->prophesize(AuthenticationUtils::class);

        $this->controller = new SecurityController(
            $this->authenticationUtilsProphecy->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /*
    public function testLoginAction()
    {
        $this->authenticationUtilsProphecy->getLastAuthenticationError()->shouldBeCalled();
        $this->authenticationUtilsProphecy->getLastUsername()->shouldBeCalled();

        $this->controller->loginAction();
    }
    */
}
