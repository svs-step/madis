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
use App\Domain\Registry\Dictionary\ProofTypeDictionary;
use App\Domain\Registry\Dictionary\ViolationNatureDictionary;
use App\Domain\User\Dictionary\UserRoleDictionary;
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

    public function __construct(
        RouterInterface $router,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Notification $repository,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->router               = $router;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider         = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDomain(): string
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModel(): string
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Notification::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function listDataTables(Request $request): JsonResponse
    {
        $user = $this->userProvider->getAuthenticatedUser();

        $criteria = [];

        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $criteria['collectivity'] = $user->getCollectivity();
        }

        if ($user) {
            if (\in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
                $criteria['collectivity'] = $user->getCollectivitesReferees();
            }
        }

        /** @var Paginator $notifications */
        $notifications = $this->getResults($request, $criteria);

        $reponse = $this->getBaseDataTablesResponse($request, $notifications, $criteria);

        /** @var Model\Notification $notification */
        foreach ($notifications as $notification) {
            $read   = '<span class="badge bg-green">' . $this->translator->trans('notification.state.read') . '</span>';
            $unread = '<span class="badge bg-orange">' . $this->translator->trans('notification.state.unread') . '</span>';

            $link = $this->getObjectLink($notification);

            $nameHtml = '<span>' . $notification->getName() . '</span> ';

            if ($link && 'notification.actions.delete' !== $notification->getAction()) {
                $nameHtml = '<a href="' . $link . '">' . $notification->getName() . '</a>'
                ;
            }
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $reponse['data'][] = [
                    'state'        => $notification->getReadAt() ? $read : $unread,
                    'module'       => $this->translator->trans($notification->getModule()),
                    'action'       => $this->translator->trans($notification->getAction()),
                    'name'         => $nameHtml,
                    'object'       => $this->getSubjectForNotification($notification),
                    'collectivity' => $this->authorizationChecker->isGranted('ROLE_REFERENT') && $notification->getCollectivity() ? $notification->getCollectivity()->getName() : '',
                    'date'         => date_format($notification->getCreatedAt(), 'd-m-Y H:i:s'),
                    'user'         => $notification->getCreatedBy() ? $notification->getCreatedBy()->__toString() : '',
                    'read_date'    => $notification->getReadAt() ? $notification->getReadAt()->format('d-m-Y H:i:s') : '',
                    'read_by'      => $notification->getReadBy() ? $notification->getReadBy()->__toString() : '',
                    'actions'      => $this->generateActionCellContent($notification),
                ];
            } else {
                $reponse['data'][] = [
                    'module'       => $this->translator->trans($notification->getModule()),
                    'action'       => $this->translator->trans($notification->getAction()),
                    'name'         => $nameHtml,
                    'object'       => $this->getSubjectForNotification($notification),
                    'date'         => date_format($notification->getCreatedAt(), 'd-m-Y H:i:s'),
                    'user'         => $notification->getCreatedBy() ? $notification->getCreatedBy()->__toString() : '',
                    'actions'      => $this->generateActionCellContent($notification),
                ];
            }

        }

        return new JsonResponse($reponse);
    }

    private function getSubjectForNotification(Notification $notification): string
    {
        if (
            'notification.modules.violation' === $notification->getModule()
        ) {
            $ob = $notification->getObject();
            if (isset($ob->violationNatures) && is_array($ob->violationNatures) && count($ob->violationNatures) > 0) {
                return join(', ', array_map(function ($v) {
                    return ViolationNatureDictionary::getNatures()[$v] ?? '';
                }, $ob->violationNatures));
            } elseif (isset($ob->violationNature)) {
                return ViolationNatureDictionary::getNatures()[$ob->violationNature] ?? '';
            }

            return '';
        }

        if (
            'notification.modules.proof' === $notification->getModule()
        ) {
            $type = $notification->getObject()->type;

            return $type && isset(ProofTypeDictionary::getTypes()[$type]) ? ProofTypeDictionary::getTypes()[$type] : '';
        }

        if ('notifications.actions.no_login' === $notification->getAction()) {
            return $this->translator->trans('notifications.subject.no_login');
        }

        if ('notifications.actions.state_change' === $notification->getAction()) {
            return $notification->getObject()->state;
        }

        if ('notifications.actions.late_survey' === $notification->getAction()) {
            $days = $this->getParameter('APP_SURVEY_NOTIFICATION_DELAY_DAYS');

            return $this->translator->trans('notifications.subject.late_survey', ['%days%' => $days]);
        }

        if ('notifications.actions.protect_action' === $notification->getAction()) {
            return $notification->getObject()->getStatus();
        }

        if ('notifications.actions.late_action' === $notification->getAction()) {
            $ob   = $notification->getObject();
            $date = \DateTime::createFromFormat(DATE_ATOM, $ob->planificationDate)->format('d/m/Y');

            return $this->translator->trans('notifications.subject.late_action', ['%date%' => $date]);
        }

        if ('notifications.actions.validation' === $notification->getAction()) {
            return $this->translator->trans('notifications.subject.validation');
        }

        if ('notifications.actions.late_request' === $notification->getAction()) {
            $days = $this->getParameter('APP_REQUEST_NOTIFICATION_DELAY_DAYS');

            return $this->translator->trans('notifications.subject.late_request', ['%days%' => $days]);
        }

        return '';
    }

    private function generateActionCellContent(Model\Notification $notification)
    {
        $id = $notification->getId();

        $user = $this->userProvider->getAuthenticatedUser();
        $html = '';

        if (null === $notification->getReadAt()) {
            $html .= '<a href="' . $this->router->generate('notification_notification_mark_as_read', ['id' => $id]) . '">
<i class="fas fa-clipboard-check"></i>&nbsp;
                ' . $this->translator->trans('notification.notification.action.mark_as_read') . '
                </a>';
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     * Here, we wanna compute maturity score.
     *
     * @param Model\Notification $object
     */
    public function formPrePersistData($object)
    {
    }

    /**
     * Update read status from notification.
     */
    public function markAsReadAllAction(Request $request)
    {
        $notifs = $this->repository->findAll();

        foreach ($notifs as $notif) {
            $isRead = $notif->getReadAt();
            if (null == $isRead) {
                $notif->setReadAt(new \DateTime());
                $notif->setReadBy($this->getUser());
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
        $notif = $this->repository->findOneByID($id);
        if (!$notif) {
            throw new NotFoundHttpException('Notification introuvable');
        }

        $notif->setReadAt(new \DateTime());
        $notif->setReadBy($this->getUser());
        $this->entityManager->flush();
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }

    /**
     * Update read_at and read_by from notification to null.
     */
    public function markAsUnreadAction(Request $request, string $id)
    {
        $notif = $this->repository->findOneByID($id);
        if (!$notif) {
            throw new NotFoundHttpException('Notification introuvable');
        }

        $notif->setReadAt(null);
        $notif->setReadBy(null);
        $this->entityManager->flush();
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }

    protected function getLabelAndKeysArray(): array
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
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
            return $this->router->generate($this->getRouteForModule($notification->getModule()), ['id' => $notification->getObject()->id], UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (\Exception $e) {
            return '';
        }
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
                return 'documentation_document_edit';
            case 'notification.modules.maturity':
                return 'maturity_survey_edit';
        }

        return '';
    }
}
