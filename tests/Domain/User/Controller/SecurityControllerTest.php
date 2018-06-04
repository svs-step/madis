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
use App\Domain\User\Component\Mailer;
use App\Domain\User\Component\TokenGenerator;
use App\Domain\User\Controller\SecurityController;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityControllerTest extends TestCase
{
    /**
     * @var ControllerHelper
     */
    private $helperProphecy;

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtilsProphecy;

    /**
     * @var TokenGenerator
     */
    private $tokenGeneratorProphecy;

    /**
     * @var Repository\User
     */
    private $userRepositoryProphecy;

    /**
     * @var Mailer
     */
    private $mailerProphecy;

    /**
     * @var SecurityController
     */
    private $controller;

    public function setUp(): void
    {
        $this->helperProphecy              = $this->prophesize(ControllerHelper::class);
        $this->authenticationUtilsProphecy = $this->prophesize(AuthenticationUtils::class);
        $this->tokenGeneratorProphecy      = $this->prophesize(TokenGenerator::class);
        $this->userRepositoryProphecy      = $this->prophesize(Repository\User::class);
        $this->mailerProphecy              = $this->prophesize(Mailer::class);

        $this->controller = new SecurityController(
            $this->helperProphecy->reveal(),
            $this->authenticationUtilsProphecy->reveal(),
            $this->tokenGeneratorProphecy->reveal(),
            $this->userRepositoryProphecy->reveal(),
            $this->mailerProphecy->reveal()
        );
    }

    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(Controller::class, $this->controller);
    }

    /**
     * Test loginAction.
     */
    public function testLoginAction(): void
    {
        $error        = null;
        $lastUsername = 'foo';

        $this->authenticationUtilsProphecy->getLastAuthenticationError()->shouldBeCalled()->willReturn($error);
        $this->authenticationUtilsProphecy->getLastUsername()->shouldBeCalled()->willReturn($lastUsername);

        $this->helperProphecy
            ->render(
                'User/Security/login.html.twig',
                [
                    'last_username' => $lastUsername,
                    'error'         => $error,
                ]
            )
            ->shouldBeCalled()
            ->willReturn(new Response())
        ;

        $this->controller->loginAction();
    }

    /**
     * Test forgetPasswordAction.
     */
    public function testForgetPasswordAction(): void
    {
        $response = new Response();

        $this->helperProphecy
            ->render('User/Security/forget_password.html.twig')
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->controller->forgetPasswordAction()
        );
    }

    /**
     * Test forgetPasswordConfirmAction
     * The provided email exists in DB.
     */
    public function testForgetPasswordConfirmationActionWithExistingEmail(): void
    {
        $email            = 'foo@email.fr';
        $request          = new Request();
        $request->request = new ParameterBag([
            'email' => $email,
        ]);
        $response = new Response();
        $token    = 'dummyToken';

        // Since email exists, user forget password token is set
        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->setForgetPasswordToken($token)->shouldBeCalled();

        $this->userRepositoryProphecy
            ->findOneOrNullByEmail($email)
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;
        $this->userRepositoryProphecy->update($userProphecy->reveal())->shouldBeCalled();

        $this->tokenGeneratorProphecy->generateToken()->shouldBeCalled()->willReturn($token);

        $this->helperProphecy
            ->render('User/Security/forget_password_confirm.html.twig')
            ->shouldBeCalled()
            ->willReturn($response)
        ;
        $this->helperProphecy
            ->redirectToRoute('forget_password')
            ->shouldNotBeCalled()
        ;
        $this->helperProphecy->addFlash('danger', Argument::type('string'))->shouldNotBeCalled();
        $this->helperProphecy
            ->trans(
                'user.security.forget_password_confirm.flashbag.error',
                [
                    '%email%' => $email,
                ]
            )
            ->shouldNotBeCalled()
        ;

        $this->mailerProphecy->sendForgetPassword($userProphecy->reveal())->shouldBeCalled();

        $this->assertEquals(
            $response,
            $this->controller->forgetPasswordConfirmAction($request)
        );
    }

    /**
     * Test forgetPasswordConfirmAction
     * The provided email doesn't exist in DB.
     */
    public function testForgetPasswordConfirmationActionWithBadEmail(): void
    {
        $email            = 'foo@email.fr';
        $request          = new Request();
        $request->request = new ParameterBag([
            'email' => $email,
        ]);
        $translatedFlashBagMessage = 'translatedFlashBagMessage';
        $response                  = new RedirectResponse('http://dummyUrl');

        // Since email doesn't exist, user forget password token is not set
        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->setForgetPasswordToken(Argument::cetera())->shouldNotBeCalled();

        $this->userRepositoryProphecy
            ->findOneOrNullByEmail($email)
            ->shouldBeCalled()
            ->willReturn(null)
        ;
        $this->userRepositoryProphecy->update($userProphecy->reveal())->shouldNotBeCalled();

        $this->tokenGeneratorProphecy->generateToken()->shouldNotBeCalled();

        $this->helperProphecy
            ->render('User/Security/forget_password_confirm.html.twig')
            ->shouldNotBeCalled()
        ;
        $this->helperProphecy
            ->redirectToRoute('forget_password')
            ->shouldBeCalled()
            ->willReturn($response)
        ;
        $this->helperProphecy->addFlash('danger', $translatedFlashBagMessage)->shouldBeCalled();
        $this->helperProphecy
            ->trans(
                'user.security.forget_password_confirm.flashbag.error',
                [
                    '%email%' => $email,
                ]
            )
            ->shouldBeCalled()
            ->willReturn($translatedFlashBagMessage)
        ;

        $this->mailerProphecy->sendForgetPassword($userProphecy->reveal())->shouldNotBeCalled();

        $this->assertEquals(
            $response,
            $this->controller->forgetPasswordConfirmAction($request)
        );
    }
}
