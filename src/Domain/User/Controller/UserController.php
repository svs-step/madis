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

use App\Application\Controller\CRUDController;
use App\Application\Interfaces\CollectivityRelated;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\User\Dictionary\UserMoreInfoDictionary;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Form\Type\UserType;
use App\Domain\User\Model;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;
use App\Domain\User\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\User $repository
 */
class UserController extends CRUDController
{
    use ServersideDatatablesTrait;
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\User $repository,
        RequestStack $requestStack,
        EncoderFactoryInterface $encoderFactory,
        Pdf $pdf,
        RouterInterface $router,
        Security $security,
        UserProvider $userProvider,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->requestStack             = $requestStack;
        $this->encoderFactory           = $encoderFactory;
        $this->router                   = $router;
        $this->security                 = $security;
        $this->userProvider             = $userProvider;
        $this->authorizationChecker     = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\User::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return UserType::class;
    }

    protected function isSoftDelete(): bool
    {
        return true;
    }

    /**
     * The unarchive action view
     * Display a confirmation message to confirm data un-archivage.
     */
    public function unarchiveAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        return $this->render($this->getTemplatingBasePath('unarchive'), [
            'object' => $object,
        ]);
    }

    /**
     * The unarchive action
     * Unarchive the data.
     *
     * @throws \Exception
     */
    public function unarchiveConfirmationAction(string $id): Response
    {
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        if (!\method_exists($object, 'setDeletedAt')) {
            throw new MethodNotImplementedException('setDeletedAt');
        }
        $object->setDeletedAt(null);
        $this->repository->update($object);

        $this->addFlash('success', $this->getFlashbagMessage('success', 'unarchive', $object));

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    public function listAction(): Response
    {
        $criteria = $this->getRequestCriteria();

        return $this->render($this->getTemplatingBasePath('list'), [
            'totalItem' => $this->repository->count($criteria),
            'route'     => $this->router->generate('user_user_list_datatables', ['archive' => $criteria['archive']]),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $criteria    = $this->getRequestCriteria();
        $users       = $this->getResults($request, $criteria);
        $reponse     = $this->getBaseDataTablesResponse($request, $users, $criteria);

        /** @var Model\User $user */
        foreach ($users as $user) {
            $roles = '';
            foreach ($user->getRoles() as $role) {
                $span = '<span class="badge ' . $this->getRolesColor($role) . '">' . UserRoleDictionary::getFullRoles()[$role] . '</span>';
                $roles .= $span;
            }

            $infos ='';
            foreach ($user->getMoreInfos() as $info) {
                $span = '<span class="badge">' . UserMoreInfoDictionary::getMoreInfos()[$info] . '</span>';
                $infos .= $span;
            }

            $userActifBgColor = 'bg-green';
            if (!$user->isEnabled()) {
                $userActifBgColor = 'bg-red';
            }

            $collectivityActifBgColor = 'bg-green';
            if (!$user->getCollectivity()->isActive()) {
                $userActifBgColor = 'bg-red';
            }

            $actif = '
                <span class="badge ' . $userActifBgColor . '">' . $this->translator->trans('user.user.title.label') . '</span>
                <span class="badge ' . $collectivityActifBgColor . '">' . $this->translator->trans('user.collectivity.title.label') . '</span>'
            ;

            $services = '<ul>';
            foreach ($user->getServices() as $service) {
                $services .= '<li>' . $service->getName() . '</li>';
            }
            $services .= '</ul>';

            $europeTimezone    = new \DateTimeZone('Europe/Paris');
            $reponse['data'][] = [
                'prenom'       => $user->getFirstName(),
                'nom'          => $user->getLastName(),
                'email'        => $user->getEmail(),
                'collectivite' => $user->getCollectivity()->getName(),
                'roles'        => $roles,
                'moreInfos'    => $infos,
                'actif'        => $actif,
                'connexion'    => !\is_null($user->getLastLogin()) ? $user->getLastLogin()->setTimezone($europeTimezone)->format('Y-m-d H:i:s') : null,
                'services'     => $services,
                'actions'      => $this->getActionCellsContent($user),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(\json_encode($reponse));

        return $jsonResponse;
    }

    protected function getLabelAndKeysArray(): array
    {
        return [
            0 => 'prenom',
            1 => 'nom',
            2 => 'email',
            3 => 'collectivite',
            4 => 'roles',
            5 => 'actif',
            6 => 'connexion',
            7 => 'services',
            8 => 'actions',
            9 => 'moreInfos',
        ];
    }

    private function getRequestCriteria()
    {
        $criteria            = [];
        $request             = $this->requestStack->getMasterRequest();
        $criteria['archive'] = $request->query->getBoolean('archive');

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            /** @var Model\User $user */
            $user                              = $this->security->getUser();
            $criteria['collectivitesReferees'] = $user->getCollectivitesReferees();
        }

        return $criteria;
    }

    private function getActionCellsContent(Model\User $user)
    {
        $cellContent = '';
        if ($this->security->getUser() !== $user && \is_null($user->getDeletedAt()) && !$this->isGranted('ROLE_PREVIOUS_ADMIN')) {
            $cellContent .=
                '<a href="' . $this->router->generate('reporting_dashboard_index', ['_switch_user' => $user->getUsername()]) . '">
                    <i class="fa fa-user-lock"></i> ' .
                $this->translator->trans('action.impersonate') .
                '</a>';
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            if (\is_null($user->getDeletedAt())) {
                $cellContent .=
                    '<a href="' . $this->router->generate('user_user_edit', ['id' => $user->getId()]) . '">
                        <i class="fa fa-pencil-alt"></i> ' .
                    $this->translator->trans('action.edit') .
                    '</a>';
            }

            if ($this->security->getUser() !== $user) {
                if (\is_null($user->getDeletedAt())) {
                    $cellContent .=
                        '<a href="' . $this->router->generate('user_user_delete', ['id' => $user->getId()]) . '">
                        <i class="fa fa-archive"></i> ' .
                        $this->translator->trans('action.archive') .
                        '</a>';
                } else {
                    $cellContent .=
                        '<a href="' . $this->router->generate('user_user_unarchive', ['id' => $user->getId()]) . '">
                        <i class="fa fa-archive"></i> ' .
                        $this->translator->trans('action.unarchive') .
                        '</a>';
                }

                $cellContent .=
                '<a href="' . $this->router->generate('user_user_delete', ['id' => $user->getId()]) . '">
                    <i class="fa fa-trash-alt"></i> ' .
                $this->translator->trans('action.delete') .
                '</a>';
            }
        }

        return $cellContent;
    }

    public function getServicesContent(string $collectivityId, string $userId): Response
    {
        $collectivity = $this->entityManager->getRepository(Collectivity::class)->findOneBy(['id' => $collectivityId]);
        if (null === $collectivity) {
            throw new NotFoundHttpException('Can\'t find collectivity for id ' . $collectivityId);
        }

        $services = $this
        ->getDoctrine()
        ->getRepository(Service::class)
        ->findBy(
            ['collectivity' => $collectivity],
            ['name' => 'ASC']
        );

        $serviceIdsSelected = [];

        if ('creer' !== $userId) {
            $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

            $servicesAlreadySelected = $user->getServices()->getValues();

            foreach ($servicesAlreadySelected as $service) {
                $serviceIdsSelected[] = $service->getId();
            }
        }

        $responseData = [];

        foreach ($services as $service) {
            $responseData[] = [
                'value'     => $service->getId()->toString(),
                'text'      => $service->__toString(),
                'selected'  => in_array($service->getId(), $serviceIdsSelected),
            ];
        }

        return new JsonResponse($responseData);
    }

    private function getRolesColor(string $role)
    {
        switch ($role) {
            case UserRoleDictionary::ROLE_ADMIN:
                return 'bg-red';
            case UserRoleDictionary::ROLE_USER:
                return 'bg-blue';
            case UserRoleDictionary::ROLE_API:
                return 'bg-green';
            default:
                return 'bg-red';
        }
    }


    /**
     * The creation action view
     * Create a new data.
     */
    public function createUser(Request $request): Response
    {
        $modelClass     = $this->getModelClass();
        /** @var User $object */
        $object         = new $modelClass();
        $serviceEnabled = false;

        if ($object instanceof CollectivityRelated) {
            $user       = $this->userProvider->getAuthenticatedUser();
            $object->setCollectivity($user->getCollectivity());
            $serviceEnabled = $object->getCollectivity()->getIsServicesEnabled();
        }

        $form = $this->createForm($this->getFormType(), $object, ['validation_groups' => ['default', $this->getModel(), 'create']]);

        $request = $this->requestStack->getMasterRequest();
        $parameters = $request->request->all();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /* Notifications */
            $notifParams = new Model\Notification();

            if (isset($parameters['is_notified'])){ $notifParams->setIsNotified($parameters['is_notified']);}
            $notifParams->setFrequency($parameters['alert']);

            switch ($parameters['alert']) {
                case 'every_hours':
                    $notifParams->setIntervalHours(intval($parameters['alert']));
                    break;
                case 'daily' :
                    $notifParams->setStartHours(intval($parameters['daily_hour']));
                    break;
                case 'weekly' :
                    $notifParams->setStartHours(intval($parameters['weekly_hour']));
                    $notifParams->setStartDay($parameters['weekly_day']);
                    break;
                case 'monthly' :
                    $notifParams->setStartHours(intval($parameters['monthly_hour']));
                    $notifParams->setStartDay($parameters['monthly_day']);
                    $notifParams->setStartWeek($parameters['monthly_week']);
                    break;
            }

            if (isset($parameters['is_treatment'])){ $notifParams->setIsTreatment($parameters['is_treatment']);}
            if (isset($parameters['is_subcontract'])){$notifParams->setIsSubcontract($parameters['is_subcontract']);}
            if (isset($parameters['is_request'])){$notifParams->setIsRequest($parameters['is_request']);}
            if (isset($parameters['is_violation'])){$notifParams->setIsViolation($parameters['is_violation']);}
            if (isset($parameters['is_proof'])){$notifParams->setIsProof($parameters['is_proof']);}
            if (isset($parameters['is_protectAction'])){$notifParams->setIsProtectAction($parameters['is_protectAction']);}
            if (isset($parameters['is_maturity'])){$notifParams->setIsMaturity($parameters['is_maturity']);}
            if (isset($parameters['is_treatmenConformity'])){$notifParams->setIsTreatmenConformity($parameters['is_treatmenConformity']);}
            if (isset($parameters['is_organizationConformity'])){$notifParams->setIsOrganizationConformity($parameters['is_organizationConformity']);}
            if (isset($parameters['is_AIPD'])){$notifParams->setIsAIPD($parameters['is_AIPD']);}
            if (isset($parameters['is_document'])){$notifParams->setIsDocument($parameters['is_document']);}

            $em = $this->getDoctrine()->getManager();
            $em->persist($notifParams);
            $em->flush();

            /* Save User with Notification */
            $object->setNotification($notifParams);
            $this->formPrePersistData($object);
            $em->persist($object);
            $em->flush();

            $this->addFlash('success', $this->getFlashbagMessage('success', 'create', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        $data = [
            'form'              => $form->createView(),
            'object'            => $object,
            'serviceEnabled'    => $serviceEnabled,
        ];

        $data['alert'] = 'none';
        $data['every_hours'] = 4;
        $data['daily_hour'] = 7;
        $data['weekly_hour'] = 7;
        $data['weekly_day'] = "Lundi";
        $data['monthly_hour']  = 7;
        $data['monthly_day'] = "Lundi";
        $data['monthly_week']  = "Premier";
        $data['is_notified'] = 'checked';
        $data['is_treatment'] = '';
        $data['is_subcontract'] = '';
        $data['is_request'] = '';
        $data['is_violation'] = '';
        $data['is_proof'] = '';
        $data['is_protectAction'] = '';
        $data['is_maturity'] = '';
        $data['is_treatmenConformity'] = '';
        $data['is_organizationConformity'] = '';
        $data['is_AIPD'] = '';
        $data['is_document'] = '';

        return $this->render($this->getTemplatingBasePath('create'), $data);
    }

    /**
     * The edition action view
     * Edit an existing data.
     *
     * @param string $id The ID of the data to edit
     */
    public function editUser(Request $request, string $id): Response
    {
        /** @var User $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $serviceEnabled = false;

        if ($object instanceof Collectivity) {
            $serviceEnabled = $object->getIsServicesEnabled();
        } elseif ($object instanceof CollectivityRelated) {
            $serviceEnabled = $object->getCollectivity()->getIsServicesEnabled();
        }

        $actionEnabled = true;
        if ($object instanceof CollectivityRelated && !$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $actionEnabled = $object->isInUserServices($this->userProvider->getAuthenticatedUser());
        }

        if (!$actionEnabled) {
            return $this->redirectToRoute($this->getRouteName('list'));
        }

        $form = $this->createForm($this->getFormType(), $object, ['validation_groups' => ['default', $this->getModel(), 'edit']]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formPrePersistData($object);
            $this->entityManager->persist($object);
            $this->entityManager->flush();

            $repository = $this->entityManager->getRepository(Model\Notification::class);
            /** @var Model\Notification $notifParams */
            $notifParams = $repository->findOneBy(['id' => $object->getNotification()->getId()]);
            $request = $this->requestStack->getMasterRequest();
            $parameters = $request->request->all();


            isset($parameters['is_notified']) ? $notifParams->setIsNotified(false) : $notifParams->setIsNotified(true);
            $notifParams->setFrequency($parameters['alert']);

            switch ($parameters['alert']) {
                case 'every_hours':
                    $notifParams->setIntervalHours(intval($parameters['alert']));
                    break;
                case 'daily' :
                    $notifParams->setStartHours(intval($parameters['daily_hour']));
                    break;
                case 'weekly' :
                    $notifParams->setStartHours(intval($parameters['weekly_hour']));
                    $notifParams->setStartDay($parameters['weekly_day']);
                    break;
                case 'monthly' :
                    $notifParams->setStartHours(intval($parameters['monthly_hour']));
                    $notifParams->setStartDay($parameters['monthly_day']);
                    $notifParams->setStartWeek($parameters['monthly_week']);
                    break;
            }

            isset($parameters['is_treatment']) ? $notifParams->setIsTreatment(true) : $notifParams->setIsTreatment(false); //$notifParams->setIsTreatment($parameters['is_treatment']);}
            isset($parameters['is_subcontract']) ? $notifParams->setIsSubcontract(true) : $notifParams->setIsSubcontract(false);
            isset($parameters['is_request']) ? $notifParams->setIsRequest(true) : $notifParams->setIsSubcontract(false);
            isset($parameters['is_violation']) ? $notifParams->setIsViolation(true) : $notifParams->setIsViolation(false);
            isset($parameters['is_proof']) ? $notifParams->setIsProof(true) : $notifParams->setIsProof(false);
            isset($parameters['is_protectAction']) ? $notifParams->setIsProtectAction(true) : $notifParams->setIsProtectAction(false);
            isset($parameters['is_maturity']) ? $notifParams->setIsMaturity(true) : $notifParams->setIsMaturity(false);
            isset($parameters['is_treatmenConformity']) ? $notifParams->setIsTreatmenConformity(true) : $notifParams->setIsTreatmenConformity(false);
            isset($parameters['is_organizationConformity']) ? $notifParams->setIsOrganizationConformity(true) : $notifParams->setIsOrganizationConformity(false);
            isset($parameters['is_AIPD']) ? $notifParams->setIsAIPD(true) : $notifParams->setIsAIPD(false);
            isset($parameters['is_document']) ? $notifParams->setIsDocument(true) : $notifParams->setIsDocument(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($notifParams);
            $em->flush();


            $this->addFlash('success', $this->getFlashbagMessage('success', 'edit', $object));

            return $this->redirectToRoute($this->getRouteName('list'));
        }

        $data = [
            'form'              => $form->createView(),
            'object'            => $object,
            'serviceEnabled'    => $serviceEnabled,
        ];
        if ($object->getNotification()){
            $data['alert'] = $object->getNotification()->getFrequency();
            $data['every_hours'] = $object->getNotification()->getIntervalHours();
            $data['daily_hour'] = $object->getNotification()->getStartHours();
            $data['weekly_hour'] = $object->getNotification()->getStartHours();
            $data['weekly_day'] = $object->getNotification()->getStartDay();
            $data['monthly_hour']  = $object->getNotification()->getStartHours();
            $data['monthly_day'] = $object->getNotification()->getStartDay();
            $data['monthly_week']  = $object->getNotification()->getStartWeek();
            $data['is_notified'] = ($object->getNotification()->getIsNotified() === false) ? 'checked' : '';
            $data['is_treatment'] = ($object->getNotification()->getIsTreatment() === true) ? 'checked' : '';
            $data['is_subcontract'] = $object->getNotification()->getIsSubcontract() === true ? 'checked' : '';
            $data['is_request'] = $object->getNotification()->getIsRequest()=== true ? 'checked' : '';
            $data['is_violation'] = $object->getNotification()->getIsViolation()=== true ? 'checked' : '';
            $data['is_proof'] = $object->getNotification()->getIsProof()=== true ? 'checked' : '';
            $data['is_protectAction'] = $object->getNotification()->getIsProtectAction()=== true ? 'checked' : '';
            $data['is_maturity'] = $object->getNotification()->getIsMaturity()=== true ? 'checked' : '';
            $data['is_treatmenConformity'] = $object->getNotification()->getIsTreatmenConformity()=== true ? 'checked' : '';
            $data['is_organizationConformity'] = $object->getNotification()->getIsOrganizationConformity()=== true ? 'checked' : '';
            $data['is_AIPD'] = $object->getNotification()->getIsAIPD()=== true ? 'checked' : '';
            $data['is_document'] = $object->getNotification()->getIsDocument()=== true ? 'checked' : '';
        }

        return $this->render($this->getTemplatingBasePath('edit'), $data);
    }

    /**
     * The delete action view
     * Display a confirmation message to confirm data deletion.
     */
    public function deleteUser(string $id): Response
    {
        /** @var CollectivityRelated $object */
        $object = $this->repository->findOneById($id);
        if (!$object) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }

        $actionEnabled = true;
        if ($object instanceof CollectivityRelated && !$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $actionEnabled = $object->isInUserServices($this->userProvider->getAuthenticatedUser());
        }

        if (!$actionEnabled) {
            return $this->redirectToRoute($this->getRouteName('list'));
        }

        return $this->render($this->getTemplatingBasePath('delete'), [
            'object' => $object,
            'id'     => $id,
        ]);
    }
}
