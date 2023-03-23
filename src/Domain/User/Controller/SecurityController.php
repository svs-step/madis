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
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SecurityController extends AbstractController
{
    private ControllerHelper $helper;
    private AuthenticationUtils $authenticationUtils;
    private TokenGenerator $tokenGenerator;
    private Repository\User $userRepository;
    private Mailer $mailer;
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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
        } catch (Exception) {
            return $this->_handleSsoClientError();
        }

        // scope openid required to get id_token needed for logout
        return $client->redirect(['openid'], []);
    }

    public function oauthCheckAction(Request $request, ClientRegistry $clientRegistry, TokenStorageInterface $tokenStorage, LoggerInterface $logger): RedirectResponse
    {
        $oauthServiceName = $request->get('service');

        /** @var OAuth2Client $client */
        $client = $clientRegistry->getClient($oauthServiceName);
        try {
            // scope openid required to get id_token needed for logout
            $accessToken   = $client->getAccessToken(['scope' => 'openid']);
            $userOAuthData = $client->fetchUserFromToken($accessToken)->toArray();
        } catch (IdentityProviderException) {
            return $this->_handleSsoClientError();
        }

        $sso_key_field = $this->sso_key_field;
        try {
            $sso_value = $userOAuthData[$sso_key_field];
        } catch (Exception) {
            $logger->error('SSO field "' . $sso_key_field . '" not found.');
            $logger->info('Data returned by SSO: ' . json_encode($userOAuthData));

            return $this->_handleSsoClientError();
        }

        $ssoLogoutUrl = null;
        $provider     = $client->getOAuth2Provider();
        if (method_exists($provider, 'getLogoutUrl')) {
            $tokenValues  = $accessToken->getValues();
            $ssoLogoutUrl = $provider->getLogoutUrl([
                'id_token_hint'            => $tokenValues['id_token'],
                'post_logout_redirect_uri' => $this->generateUrl('login', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            $session = $request->getSession();
            $session->set('ssoLogoutUrl', $ssoLogoutUrl);
        }

        $currentUser = $this->userProvider->getAuthenticatedUser();
        if ($currentUser) {
            return $this->_associateUserWithSsoKey($sso_value, $currentUser);
        }

        $user = $this->userRepository->findOneOrNullBySsoKey($sso_value);
        if (!$user) {
            return $this->_handleUserNotFound($ssoLogoutUrl);
        }

        // Login Programmatically
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'public', $user->getRoles());
        $tokenStorage->setToken($token);

        return $this->helper->redirectToRoute('reporting_dashboard_index');
    }

    /**
     * Display Forget password page.
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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

    private function _handleUserNotFound(string $logoutUrl = null): RedirectResponse
    {
        $this->helper->addFlash(
            'danger',
            $this->helper->trans('user.security.reset_password.flashbag.error')
        );

        if ($logoutUrl) {
            return $this->redirect($logoutUrl);
        }

        return $this->helper->redirectToRoute('login');
    }

    private function _handleDuplicateUserWithSsoKey(User $alreadyExists): RedirectResponse
    {
        $this->helper->addFlash('danger',
            $this->helper->trans('user.profile.flashbag.error.sso_key_duplicate', ['email' => $alreadyExists->getEmail()])
        );

        return $this->helper->redirectToRoute('user_profile_user_edit');
    }

    private function _associateUserWithSsoKey(mixed $sso_value, User $currentUser): RedirectResponse
    {
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
}
