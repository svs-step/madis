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

namespace App\Domain\Notification\Controller;

use App\Application\Controller\CRUDController;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\ServersideDatatablesTrait;
use App\Domain\Notification\Model;
use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Repository;
use App\Domain\User\Dictionary\UserMoreInfoDictionary;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Infrastructure\ORM\AIPD\Repository\AnalyseImpact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Notification $repository
 */
class NotificationController extends CRUDController
{
    use ServersideDatatablesTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    protected AnalyseImpact $aipdRepository;

    public function __construct(
        RouterInterface $router,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Notification $repository,
        AnalyseImpact $aipdRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->router               = $router;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
        $this->aipdRepository       = $aipdRepository;
    }

    protected function getDomain(): string
    {
        return 'notification';
    }

    protected function getModel(): string
    {
        return 'notification';
    }

    protected function getModelClass(): string
    {
        return Notification::class;
    }

    protected function getFormType(): string
    {
        return '';
    }

    protected function getListData()
    {
        $order = [
            'createdAt' => 'DESC',
        ];

        return $this->repository->findAll($order);
    }

    public function listAction(): Response
    {
        $user = $this->getUser();

        $criteria = [];

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $this->userProvider->getAuthenticatedUser()->getCollectivity();
        }

        return $this->render('Notification/Notification/list.html.twig', [
            'totalItem' => $this->repository->count($criteria),
            'route'     => $this->router->generate('notification_notification_list_datatables'),
        ]);
    }

    public function listDataTables(Request $request): JsonResponse
    {
        $user = $this->userProvider->getAuthenticatedUser();

        $criteria = [];

        if (!$this->authorizationChecker->isGranted('ROLE_REFERENT')) {
            $criteria['collectivity'] = new ArrayCollection([$user->getCollectivity(), null]);
        }

        if ($user) {
            if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
                $criteria['collectivity'] = $user->getCollectivitesReferees();
            }
        }

        /** @var Paginator $notifications */
        $notifications = $this->getResults($request, $criteria);

        $reponse = $this->getBaseDataTablesResponse($request, $notifications, $criteria);

        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            $read   = '<span class="badge bg-green">' . $this->translator->trans('notifications.label.read') . '</span>';
            $unread = '<span class="badge bg-yellow">' . $this->translator->trans('notifications.label.unread') . '</span>';

            $link = $this->getObjectLink($notification);

            $nameHtml = '<span>' . $notification->getName() . '</span> ';

            if ($link && 'notification.actions.delete' !== $notification->getAction() && $this->repository->objectExists($notification)) {
                $nameHtml = '<a href="' . $link . '">' . $notification->getName() . '</a>'
                ;
            }

            if ($this->authorizationChecker->isGranted('ROLE_REFERENT') || in_array(UserMoreInfoDictionary::MOREINFO_DPD, $user->getMoreInfos())) {
                $reponse['data'][] = [
                    'state'        => $notification->getReadAt() ? $read : $unread,
                    'module'       => $this->translator->trans($notification->getModule()),
                    'action'       => $this->translator->trans($notification->getAction()),
                    'name'         => $nameHtml,
                    'object'       => $notification->getSubject(),
                    'collectivity' => $this->authorizationChecker->isGranted('ROLE_REFERENT') && $notification->getCollectivity() ? $notification->getCollectivity()->getName() : '',
                    'date'         => date_format($notification->getCreatedAt(), 'd-m-Y H:i'),
                    'user'         => $notification->getCreatedBy() ? $notification->getCreatedBy()->__toString() : '',
                    'read_date'    => $notification->getReadAt() ? $notification->getReadAt()->format('d-m-Y H:i') : '',
                    'read_by'      => $notification->getReadBy() ? $notification->getReadBy()->__toString() : '',
                    'actions'      => $this->generateActionCellContent($notification),
                ];
            } else {
                $notificationUser = $notification->getNotificationUsers()->findFirst(function ($i, Model\NotificationUser $nu) use ($user) {
                    return $nu->getUser() && $nu->getUser()->getId() === $user->getId();
                });

                $reponse['data'][] = [
                    'state'   => $notificationUser->getReadAt() ? $read : $unread,
                    'module'  => $this->translator->trans($notification->getModule()),
                    'action'  => $this->translator->trans($notification->getAction()),
                    'name'    => $nameHtml,
                    'object'  => $notification->getSubject(),
                    'date'    => date_format($notification->getCreatedAt(), 'd-m-Y H:i'),
                    'user'    => $notification->getCreatedBy() ? $notification->getCreatedBy()->__toString() : '',
                    'actions' => $this->generateActionCellContent($notification),
                ];
            }
        }

        return new JsonResponse($reponse);
    }

    private function generateActionCellContent(Notification $notification)
    {
        $id = $notification->getId();

        $user = $this->userProvider->getAuthenticatedUser();
        $html = '';

        if (
            in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_REFERENT', $user->getRoles())
            || in_array(UserMoreInfoDictionary::MOREINFO_DPD, $user->getMoreInfos())
        ) {
            if (null === $notification->getReadAt()) {
                $html .= '<a href="' . $this->router->generate('notification_notification_mark_as_read', ['id' => $id]) . '">
<i aria-hidden="true" class="fas fa-clipboard-check"></i>&nbsp;
                ' . $this->translator->trans('notifications.action.mark_as_read') . '
                </a>';
            } else {
                $html .= ' <a href="' . $this->router->generate('notification_notification_mark_as_unread', ['id' => $id]) . '">
<i aria-hidden="true" class="fas fa-clipboard"></i>&nbsp;
                ' . $this->translator->trans('notifications.action.mark_as_unread') . '
                </a>';
            }
        } else {
            $notificationUser = $notification->getNotificationUsers()->findFirst(function ($i, Model\NotificationUser $nu) use ($user) {
                return $nu->getUser() && $nu->getUser()->getId() === $user->getId();
            });
            if ($notificationUser && null === $notificationUser->getReadAt()) {
                $html .= '<a href="' . $this->router->generate('notification_notification_mark_as_read', ['id' => $id]) . '">
<i aria-hidden="true" class="fas fa-clipboard-check"></i>&nbsp;
                ' . $this->translator->trans('notifications.action.mark_as_read') . '
                </a>';
            } else {
                $html .= ' <a href="' . $this->router->generate('notification_notification_mark_as_unread', ['id' => $id]) . '">
<i aria-hidden="true" class="fas fa-clipboard"></i>&nbsp;
                ' . $this->translator->trans('notifications.action.mark_as_unread') . '
                </a>';
            }
        }

        $html .= ' <a href="' . $this->router->generate('notification_notification_delete', ['id' => $id]) . '">
<i aria-hidden="true" class="fas fa-trash"></i>&nbsp;
                ' . $this->translator->trans('global.action.delete') . '
                </a>';

        return $html;
    }

    /**
     * {@inheritdoc}
     * Here, we wanna compute maturity score.
     *
     * @param Notification $object
     */
    public function formPrePersistData($object)
    {
    }

    /**
     * Update read status from notification.
     */
    public function markAsReadAllAction(Request $request)
    {
        $notifications = $this->repository->findAll();
        $user          = $this->userProvider->getAuthenticatedUser();
        foreach ($notifications as $notification) {
            if (is_array($notification)) {
                $notification = $notification[0];
            }
            if ($notification->getDpo() && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_REFERENT', $user->getRoles()) || in_array(UserMoreInfoDictionary::MOREINFO_DPD, $user->getMoreInfos()))) {
                $notification->setReadAt(new \DateTime());
                $notification->setReadBy($this->getUser());
            } else {
                $nu = $notification->getNotificationUsers()->findFirst(function ($i, $n) use ($user) {
                    /* @var Model\NotificationUser $n */
                    return $n->getUser() && $n->getUser()->getId() === $user->getId();
                });
                if ($nu) {
                    $nu->setReadAt(new \DateTime());
                }
            }
        }

        $this->entityManager->flush();

        $this->addFlash('success', $this->getFlashbagMessage('success', 'markall'));

        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
        // return $this->redirectToRoute($this->getRouteName('list'));
    }

    /**
     * Update read_at and read_by from notification.
     */
    public function markAsReadAction(Request $request, string $id)
    {
        $notification = $this->repository->findOneByID($id);
        if (!$notification) {
            throw new NotFoundHttpException('Notification introuvable');
        }

        $user = $this->userProvider->getAuthenticatedUser();

        if ($notification->getDpo() && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_REFERENT', $user->getRoles()) || in_array(UserMoreInfoDictionary::MOREINFO_DPD, $user->getMoreInfos()))) {
            $notification->setReadAt(new \DateTime());
            $notification->setReadBy($this->getUser());
            $this->entityManager->flush();
        } else {
            $nu = $notification->getNotificationUsers()->findFirst(function ($i, $n) use ($user) {
                /* @var Model\NotificationUser $n */
                return $n->getUser() && $n->getUser()->getId() === $user->getId();
            });
            if ($nu) {
                $nu->setReadAt(new \DateTime());
                $this->entityManager->flush();
            }
        }

        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }

    /**
     * Update read_at and read_by from notification to null.
     */
    public function markAsUnreadAction(Request $request, string $id)
    {
        $notification = $this->repository->findOneByID($id);
        if (!$notification) {
            throw new NotFoundHttpException('Notification introuvable');
        }
        $user = $this->userProvider->getAuthenticatedUser();
        if ($notification->getDpo() && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_REFERENT', $user->getRoles()) || in_array(UserMoreInfoDictionary::MOREINFO_DPD, $user->getMoreInfos()))) {
            $notification->setReadAt(null);
            $notification->setReadBy(null);
            $this->entityManager->flush();
        } else {
            $user = $this->userProvider->getAuthenticatedUser();
            $nu   = $notification->getNotificationUsers()->findFirst(function ($i, $n) use ($user) {
                /* @var Model\NotificationUser $n */
                return $n->getUser() && $n->getUser()->getId() === $user->getId();
            });
            if ($nu) {
                $nu->setReadAt(null);
                $this->entityManager->flush();
            }
        }
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }

    protected function getLabelAndKeysArray(): array
    {
        $user = $this->userProvider->getAuthenticatedUser();
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN') || $this->authorizationChecker->isGranted('ROLE_REFERENT') || in_array(UserMoreInfoDictionary::MOREINFO_DPD, $user->getMoreInfos())) {
            return [
                'state',
                'module',
                'action',
                'name',
                'object',
                'collectivity',
                'date',
                'user',
                'read_date',
                'read_by',
            ];
        }

        return [
            'state',
            'module',
            'action',
            'name',
            'object',
            'date',
            'user',
        ];
    }

    private function getObjectLink(Notification $notification): string
    {
        try {
            if ('notification.modules.aipd' === $notification->getModule() && 'notification.actions.validation' === $notification->getAction()) {
                return $this->router->generate('aipd_analyse_impact_validation', ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            if ('notification.modules.aipd' === $notification->getModule() && 'notification.actions.state_change' === $notification->getAction()) {
                return $this->router->generate('aipd_analyse_impact_evaluation', ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            if ('notification.modules.aipd' === $notification->getModule() && 'notification.actions.validated' === $notification->getAction()) {
                /** @var \App\Domain\AIPD\Model\AnalyseImpact $aipd */
                $aipd = $this->aipdRepository->findOneById($notification->getObject()->id);

                return $this->router->generate('registry_treatment_show', ['id' => $aipd->getConformiteTraitement()->getTraitement()->getId()->toString()], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            if ('notification.modules.aipd' === $notification->getModule() && 'notifications.actions.treatment_needs_aipd' === $notification->getAction()) {
                return $this->router->generate('registry_conformite_traitement_start_aipd', ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
            }
            if ('notification.modules.document' === $notification->getModule() && 'notification.actions.delete' !== $notification->getAction()) {
                return $notification->getObject()->url;
            }
            if ('notification.modules.action_plan' === $notification->getModule()) {
                return $this->router->generate('registry_mesurement_show', ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
            }

            return $this->router->generate($this->getRouteForModule($notification->getModule()), ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (\Exception $e) {
            return '';
        }
    }

    public function deleteConfirmationAction(string $id): Response
    {
        /** @var Notification $notification */
        $notification = $this->repository->findOneById($id);
        if (!$notification) {
            throw new NotFoundHttpException("No object found with ID '{$id}'");
        }
        if ($notification->getDpo()) {
            $this->entityManager->remove($notification);
            $this->entityManager->flush();
        } else {
            $user = $this->userProvider->getAuthenticatedUser();
            $nu   = $notification->getNotificationUsers()->findFirst(function ($i, $n) use ($user) {
                /* @var Model\NotificationUser $n */
                return $n->getUser()->getId() === $user->getId();
            });
            if ($nu) {
                $this->entityManager->remove($nu);
                $this->entityManager->flush();
            }
        }

        $this->addFlash('success', $this->getFlashbagMessage('success', 'delete', $notification));

        return $this->redirectToRoute($this->getRouteName('list'));
    }

    private function getRouteForModule($module): string
    {
        switch ($module) {
            case 'notification.modules.treatment':
                return 'registry_treatment_show';
            case 'notification.modules.subcontractor':
            case 'notification.modules.contractor':
                return 'registry_contractor_show';
            case 'notification.modules.violation':
                return 'registry_violation_show';
            case 'notification.modules.proof':
                return 'registry_proof_edit';
            case 'notification.modules.protect_action':
                return 'registry_mesurement_show';
            case 'notification.modules.request':
                return 'registry_request_show';
            case 'notification.modules.user':
                return 'user_user_edit';
            case 'notification.modules.documentation':
            case 'notification.modules.document':
                return 'documentation_document_download';
            case 'notification.modules.maturity':
                return 'maturity_survey_edit';
        }

        return '';
    }
}
