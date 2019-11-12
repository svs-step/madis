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

namespace App\Domain\User\Controller;

use App\Application\Controller\ControllerHelper;
use App\Domain\User\Component\Mailer;
use App\Domain\User\Component\TokenGenerator;
use App\Domain\User\Form\Type\ResetPasswordType;
use App\Domain\User\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var ControllerHelper
     */
    private $helper;

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var Repository\User
     */
    private $userRepository;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        ControllerHelper $helper,
        AuthenticationUtils $authenticationUtils,
        TokenGenerator $tokenGenerator,
        Repository\User $userRepository,
        Mailer $mailer
    ) {
        $this->helper              = $helper;
        $this->authenticationUtils = $authenticationUtils;
        $this->tokenGenerator      = $tokenGenerator;
        $this->userRepository      = $userRepository;
        $this->mailer              = $mailer;
    }

    /**
     * Display login page.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function loginAction(): Response
    {
        $error        = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->helper->render('User/Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * Display Forget password page.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function forgetPasswordAction(): Response
    {
        return $this->helper->render('User/Security/forget_password.html.twig');
    }

    /**
     * Forget password confirmation
     * - Check if email exists on DB (redirect to forgetPasswordAction is not exists)
     * - Generate user token
     * - Send forget password email
     * - Display forget password confirmation page.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function forgetPasswordConfirmAction(Request $request): Response
    {
        $email = $request->request->get('email');
        $user  = $this->userRepository->findOneOrNullByEmail($email);

        if (!$user) {
            $this->helper->addFlash(
                'danger',
                $this->helper->trans(
                    'user.security.forget_password_confirm.flashbag.error',
                    [
                        '%email%' => $email,
                    ]
                )
            );

            return $this->helper->redirectToRoute('forget_password');
        }

        $user->setForgetPasswordToken($this->tokenGenerator->generateToken());
        $this->userRepository->update($user);

        $this->mailer->sendForgetPassword($user);

        return $this->helper->render('User/Security/forget_password_confirm.html.twig');
    }

    /**
     * Reset user password
     * - Token does not exists in DB: redirect to login page with flashbag error
     * - Token exists in DB: show reset password form for related user.
     *
     * @param Request $request The Request
     * @param string  $token   The forgetPasswordToken to search the user with
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function resetPasswordAction(Request $request, string $token): Response
    {
        $user = $this->userRepository->findOneOrNullByForgetPasswordToken($token);

        // If user doesn't exists, add flashbag error & return to login page
        if (!$user) {
            $this->helper->addFlash(
                'danger',
                $this->helper->trans('user.security.reset_password.flashbag.error')
            );

            return $this->helper->redirectToRoute('login');
        }

        // User exist, display reset password form
        $form = $this->helper->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Remove forgetPasswordToken on user since password is not reset
            $user->setForgetPasswordToken(null);
            $this->userRepository->update($user);

            $this->helper->addFlash('success', $this->helper->trans('user.security.reset_password.flashbag.success'));

            return $this->helper->redirectToRoute('login');
        }

        return $this->helper->render('User/Security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
