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
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AbstractController
{
    // use ControllerTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ControllerHelper
     */
    private $helper;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Repository\Collectivity
     */
    private $collectivityRepository;

    /**
     * @var Repository\User
     */
    private $userRepository;
    private ?string $sso_type;

    public function __construct(
        EntityManagerInterface $entityManager,
        ControllerHelper $helper,
        RequestStack $requestStack,
        UserProvider $userProvider,
        Repository\Collectivity $collectivityRepository,
        Repository\User $userRepository,
        ?string $sso_type
    ) {
        $this->entityManager          = $entityManager;
        $this->helper                 = $helper;
        $this->requestStack           = $requestStack;
        $this->userProvider           = $userProvider;
        $this->collectivityRepository = $collectivityRepository;
        $this->userRepository         = $userRepository;
        $this->sso_type               = $sso_type;
    }

    /**
     * Show user collectivity information.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function collectivityShowAction(): Response
    {
        $object = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        return $this->helper->render('User/Profile/collectivity_show.html.twig', [
            'object' => $object,
        ]);
    }

    /**
     * Generate collectivity edit form for user.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function collectivityEditAction(): Response
    {
        $request = $this->requestStack->getMasterRequest();
        $object  = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        $form    = $this->helper->createForm(
            CollectivityType::class,
            $object,
            [
                'validation_groups' => [
                    'default',
                    'collectivity_user',
                    'edit',
                ],
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->collectivityRepository->update($object);

            $this->helper->addFlash('success', $this->helper->trans('user.profile.flashbag.success.collectivity_edit'));

            return $this->helper->redirectToRoute('user_profile_collectivity_show', ['id' => $object->getId()]);
        }

        return $this->helper->render('User/Profile/collectivity_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Generate user edit form.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function userEditAction(): Response
    {
        $request = $this->requestStack->getMasterRequest();
        $object  = $this->userProvider->getAuthenticatedUser();

        $services = false;

        if ($object) {
            $services = $object->getServices();
        }

        $form = $this->helper->createForm(
            UserType::class,
            $object,
            [
                'validation_groups' => [
                    'default',
                    'collectivity_user',
                    'edit',
                ],
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->update($object);

            $this->helper->addFlash('success', $this->helper->trans('user.profile.flashbag.success.user_edit'));

            return $this->helper->redirectToRoute('user_profile_user_edit');
        }

        return $this->helper->render('User/Profile/user_edit.html.twig', [
            'form'           => $form->createView(),
            'roles'          => $object->getRoles(),
            'services'       => $services,
            'sso_type'       => $this->sso_type,
            'sso_associated' => null !== $object->getSsoKey(),
        ]);
    }

    public function userSsoUnlinkAction(): Response
    {
        $object = $this->userProvider->getAuthenticatedUser();
        $object->setSsoKey(null);
        $this->entityManager->persist($object);
        $this->entityManager->flush();
        $this->helper->addFlash('success',
            $this->helper->trans('user.profile.flashbag.success.sso_unlink')
        );

        return $this->helper->redirectToRoute('user_profile_user_edit');
    }
}
