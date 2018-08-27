<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\User\Controller;

use App\Application\Controller\ControllerHelper;
use App\Application\Symfony\Security\UserProvider;
use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Repository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ProfileController
{
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

    public function __construct(
        ControllerHelper $helper,
        RequestStack $requestStack,
        UserProvider $userProvider,
        Repository\Collectivity $collectivityRepository,
        Repository\User $userRepository
    ) {
        $this->helper                 = $helper;
        $this->requestStack           = $requestStack;
        $this->userProvider           = $userProvider;
        $this->collectivityRepository = $collectivityRepository;
        $this->userRepository         = $userRepository;
    }

    /**
     * Show user collectivity information.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function collectivityEditAction(): Response
    {
        $request      = $this->requestStack->getMasterRequest();
        $object       = $this->userProvider->getAuthenticatedUser()->getCollectivity();

        $form = $this->helper->createForm(
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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function userEditAction(): Response
    {
        $request = $this->requestStack->getMasterRequest();
        $object  = $this->userProvider->getAuthenticatedUser();

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
            'form' => $form->createView(),
        ]);
    }
}
