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

namespace App\Domain\User\Controller;

use App\Application\Controller\ControllerHelper;
use App\Domain\User\Component\Mailer;
use App\Domain\User\Component\TokenGenerator;
use App\Domain\User\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
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
     * @param Request $request
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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
}
