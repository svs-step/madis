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
use App\Domain\Notification\Model;
use App\Domain\Notification\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property Repository\Notification $repository
 */
class NotificationController extends CRUDController
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProvider
     */
    protected $userProvider;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Repository\Notification $repository,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->requestStack           = $requestStack;
        $this->authorizationChecker   = $authorizationChecker;
        $this->userProvider           = $userProvider;
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

        $isAdminView   = $this->authorizationChecker->isGranted('ROLE_ADMIN');

        if ($isAdminView) {
            return $this->render($this->getTemplatingBasePath('list_admin'), [
                'objects'    => $this->getListData(),
            ]);
        } else {
            return $this->render($this->getTemplatingBasePath('list_user'), [
                'objects'    => $this->getListData(),
            ]);
        }
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
            if ($isRead == null) {
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
     * Update read_at and read_by from notification to null
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
}
