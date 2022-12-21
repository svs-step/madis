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
use App\Application\Symfony\Security\UserProvider;
use App\Domain\User\Component\Mailer;
use App\Domain\User\Component\TokenGenerator;
use App\Domain\User\Form\Type\ResetPasswordType;
use App\Domain\User\Model\User;
use App\Domain\User\Repository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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

    private UserProvider $userProvider;

    private EntityManagerInterface $entityManager;

    private ?string $sso_type;
    private ?string $sso_key_field;

    public function __construct(
        ControllerHelper $helper,
        AuthenticationUtils $authenticationUtils,
        TokenGenerator $tokenGenerator,
        Repository\User $userRepository,
        Mailer $mailer,
        UserProvider $userProvider,
        EntityManagerInterface $entityManager,
        ?string $sso_type,
        ?string $sso_key_field
    ) {
        $this->helper              = $helper;
        $this->authenticationUtils = $authenticationUtils;
        $this->tokenGenerator      = $tokenGenerator;
        $this->userRepository      = $userRepository;
        $this->mailer              = $mailer;
        $this->userProvider        = $userProvider;
        $this->entityManager       = $entityManager;
        $this->sso_type            = $sso_type;
        $this->sso_key_field       = $sso_key_field;
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
            'sso_type'      => $this->sso_type,
        ]);
    }

    public function oauthConnectAction(Request $request, ClientRegistry $clientRegistry): RedirectResponse
    {
        $currentUser = $this->userProvider->getAuthenticatedUser();
        if ($currentUser && null !== $currentUser->getSsoKey()) {
            return $this->_handleUserLoggedAlreadyAssociated();
        }

        $oauthServiceName = $request->get('service');
        try {
            $client = $clientRegistry->getClient($oauthServiceName);
        } catch (\Exception) {
            return $this->_handleSsoClientError();
        }

        return $client->redirect([], []);
    }

    public function oauthCheckAction(Request $request, ClientRegistry $clientRegistry, TokenStorageInterface $tokenStorage)
    {
        $oauthServiceName = $request->get('service');

        /** @var OAuth2Client $client */
        $client = $clientRegistry->getClient($oauthServiceName);
        try {
            $accessToken   = $client->getAccessToken();
            $userOAuthData = $client->fetchUserFromToken($accessToken)->toArray();
        } catch (IdentityProviderException) {
            return $this->_handleSsoClientError();
        }

        $sso_key_field = $this->sso_key_field;
        try {
            $sso_value = $userOAuthData[$sso_key_field];
        } catch (\Exception) {
            // sso_key_field not found
            return $this->_handleSsoClientError();
        }

        $currentUser = $this->userProvider->getAuthenticatedUser();
        if ($currentUser) {
            $alreadyExists = $this->userRepository->findOneOrNullBySsoKey($sso_value);
            if ($alreadyExists) {
                return $this->_handleDuplicateUserWithSsoKey($alreadyExists);
            }

            // associate user with sso key
            $currentUser->setSsoKey($sso_value);
            $this->entityManager->persist($currentUser);
            $this->entityManager->flush();
            $this->helper->addFlash('success',
                $this->helper->trans('user.profile.flashbag.success.sso_associated')
            );

            return $this->helper->redirectToRoute('user_profile_user_edit');
        }

        $user = $this->userRepository->findOneOrNullBySsoKey($sso_value);
        if (!$user) {
            return $this->_handleUserNotFound();
        }

        // Login Programmatically
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'public', $user->getRoles());
        $tokenStorage->setToken($token);

        return $this->helper->redirectToRoute('reporting_dashboard_index');
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
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function forgetPasswordConfirmAction(Request $request): Response
    {
        $email = $request->request->get('email');
        $user  = $this->userRepository->findOneOrNullByEmail($email);

        if (!$user) {
            return $this->helper->render('User/Security/forget_password_confirm.html.twig');
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
            return $this->_handleUserNotFound();
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

    private function _handleUserLoggedAlreadyAssociated(): RedirectResponse
    {
        $this->helper->addFlash('warning',
            $this->helper->trans('user.profile.flashbag.error.sso_already_associated')
        );

        return $this->helper->redirectToRoute('user_profile_user_edit');
    }

    private function _handleSsoClientError(): RedirectResponse
    {
        $this->helper->addFlash('danger',
            $this->helper->trans('user.security.sso_login.flashbag.sso_client_error')
        );

        return $this->helper->redirectToRoute('login');
    }

    private function _handleUserNotFound(): RedirectResponse
    {
        $this->helper->addFlash(
            'danger',
            $this->helper->trans('user.security.reset_password.flashbag.error')
        );

        return $this->helper->redirectToRoute('login');
    }

    private function _handleDuplicateUserWithSsoKey(User $alreadyExists): RedirectResponse
    {
        $this->helper->addFlash('danger',
            $this->helper->trans('user.profile.flashbag.error.sso_key_duplicate', ['email' => $alreadyExists->getEmail()])
        );

        return $this->helper->redirectToRoute('user_profile_user_edit');
    }
}
