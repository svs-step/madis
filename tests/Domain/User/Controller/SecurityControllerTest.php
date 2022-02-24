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
use App\Domain\User\Component\Mailer;
use App\Domain\User\Component\TokenGenerator;
use App\Domain\User\Controller\SecurityController;
use App\Domain\User\Form\Type\ResetPasswordType;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityControllerTest extends TestCase
{
    use ProphecyTrait;
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
        $this->assertInstanceOf(AbstractController::class, $this->controller);
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
        $response                  = new Response();

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

        $this->mailerProphecy->sendForgetPassword($userProphecy->reveal())->shouldNotBeCalled();

        $this->assertEquals(
            $response,
            $this->controller->forgetPasswordConfirmAction($request)
        );
    }

    /**
     * Test resetPasswordAction
     * Method GET.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testResetPasswordActionMethodGet()
    {
        $forgetPasswordToken = 'foo';
        $user                = new Model\User();
        $user->setForgetPasswordToken($forgetPasswordToken);
        $response = new Response();

        $this->userRepositoryProphecy
            ->findOneOrNullByForgetPasswordToken($forgetPasswordToken)
            ->shouldBeCalled()
            ->willReturn($user)
        ;
        $this->userRepositoryProphecy
            ->update($user)
            ->shouldNotBeCalled()
        ;

        // FlashBag & Translation (only display, no flashbag)
        $this->helperProphecy->addFlash('danger', Argument::any())->shouldNotBeCalled();
        $this->helperProphecy->addFlash('success', Argument::any())->shouldNotBeCalled();
        $this->helperProphecy->trans('user.security.reset_password.flashbag.error')->shouldNotBeCalled();
        $this->helperProphecy->trans('user.security.reset_password.flashbag.success')->shouldNotBeCalled();

        // Form
        $formView     = $this->prophesize(FormView::class)->reveal();
        $formProphecy = $this->prophesize(FormInterface::class);
        $formProphecy->createView()->shouldBeCalled()->willReturn($formView);
        $formProphecy->handleRequest(Argument::type(Request::class))->shouldBeCalled();
        $formProphecy->isSubmitted()->shouldBeCalled()->willReturn(false);
        $this->helperProphecy
            ->createForm(ResetPasswordType::class, $user)
            ->shouldBeCalled()
            ->willReturn($formProphecy->reveal())
        ;

        // Routing & rendering
        $this->helperProphecy->redirectToRoute('login')->shouldNotBeCalled();
        $this->helperProphecy
            ->render(
                'User/Security/reset_password.html.twig',
                [
                    'form' => $formView,
                ]
            )
            ->shouldBeCalled()
            ->willReturn($response)
        ;

        $this->assertEquals(
            $response,
            $this->controller->resetPasswordAction(new Request(), $forgetPasswordToken)
        );
    }

    /**
     * Test resetPasswordAction
     * Method POST.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testResetPasswordActionMethodPost()
    {
        $forgetPasswordToken = 'foo';
        $response            = $this->prophesize(RedirectResponse::class)->reveal();
        $userProphecy        = $this->prophesize(Model\User::class);
        $userProphecy->setForgetPasswordToken(null)->shouldBeCalled();

        $this->userRepositoryProphecy
            ->findOneOrNullByForgetPasswordToken($forgetPasswordToken)
            ->shouldBeCalled()
            ->willReturn($userProphecy->reveal())
        ;
        $this->userRepositoryProphecy
            ->update($userProphecy->reveal())
            ->shouldBeCalled()
        ;

        // FlashBag & Translation (only display, no flashbag)
        $this->helperProphecy->addFlash('danger', Argument::any())->shouldNotBeCalled();
        $this->helperProphecy->addFlash('success', Argument::any())->shouldBeCalled();
        $this->helperProphecy->trans('user.security.reset_password.flashbag.error')->shouldNotBeCalled();
        $this->helperProphecy->trans('user.security.reset_password.flashbag.success')->shouldBeCalled()->willReturn('Foo');

        // Form
        $formProphecy = $this->prophesize(FormInterface::class);
        $formProphecy->createView()->shouldNotBeCalled();
        $formProphecy->handleRequest(Argument::type(Request::class))->shouldBeCalled();
        $formProphecy->isSubmitted()->shouldBeCalled()->willReturn(true);
        $formProphecy->isValid()->shouldBeCalled()->willReturn(true);
        $this->helperProphecy
            ->createForm(ResetPasswordType::class, $userProphecy->reveal())
            ->shouldBeCalled()
            ->willReturn($formProphecy->reveal())
        ;

        // Routing & rendering
        $this->helperProphecy->redirectToRoute('login')->shouldBeCalled()->willReturn($response);
        $this->helperProphecy
            ->render('User/Security/reset_password.html.twig', Argument::type('array'))
            ->shouldNotBeCalled()
        ;

        $this->assertEquals(
            $response,
            $this->controller->resetPasswordAction(new Request(), $forgetPasswordToken)
        );
    }

    /**
     * Test resetPasswordAction
     * User isn't found.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testResetPasswordActionUserNotFound()
    {
        $forgetPasswordToken = 'foo';
        $response            = $this->prophesize(RedirectResponse::class)->reveal();

        $this->userRepositoryProphecy
            ->findOneOrNullByForgetPasswordToken($forgetPasswordToken)
            ->shouldBeCalled()
            ->willReturn(null)
        ;
        $this->userRepositoryProphecy
            ->update(Argument::type(Model\User::class))
            ->shouldNotBeCalled()
        ;

        // FlashBag & Translation (only display, no flashbag)
        $this->helperProphecy->addFlash('danger', Argument::any())->shouldBeCalled();
        $this->helperProphecy->addFlash('success', Argument::any())->shouldNotBeCalled();
        $this->helperProphecy->trans('user.security.reset_password.flashbag.error')->shouldBeCalled()->willReturn('Foo');
        $this->helperProphecy->trans('user.security.reset_password.flashbag.success')->shouldNotBeCalled();

        // Form
        $formProphecy = $this->prophesize(FormInterface::class);
        $formProphecy->createView()->shouldNotBeCalled();
        $formProphecy->handleRequest(Argument::type(Request::class))->shouldNotBeCalled();
        $formProphecy->isSubmitted()->shouldNotBeCalled();
        $formProphecy->isValid()->shouldNotBeCalled();
        $this->helperProphecy
            ->createForm(ResetPasswordType::class, Argument::type(Model\User::class))
            ->shouldNotBeCalled()
        ;

        // Routing & rendering
        $this->helperProphecy->redirectToRoute('login')->shouldBeCalled()->willReturn($response);
        $this->helperProphecy
            ->render('User/Security/reset_password.html.twig', Argument::type('array'))
            ->shouldNotBeCalled()
        ;

        $this->assertEquals(
            $response,
            $this->controller->resetPasswordAction(new Request(), $forgetPasswordToken)
        );
    }
}
