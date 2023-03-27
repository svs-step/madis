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
use App\Infrastructure\ORM\Notification\Repository\NotificationUser;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationUserController extends CRUDController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        NotificationUser $repository,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProvider $userProvider,
        Pdf $pdf
    ) {
        parent::__construct($entityManager, $translator, $repository, $pdf, $userProvider, $authorizationChecker);
        $this->repository = $repository;
    }

    protected function getDomain(): string
    {
        return 'notification';
    }

    protected function getModel(): string
    {
        return 'notification_user';
    }

    protected function getModelClass(): string
    {
        return Model\NotificationUser::class;
    }

    protected function getFormType(): string
    {
        return '';
    }

    public function unsubscribe($id, $notif_id)
    {
        $notifUser = $this->repository->findOneById($id);

        if ($notifUser && $notifUser->getNotification() && $notifUser->getNotification()->getId() === $notif_id) {
            $notifUser->setActive(false);
            $this->entityManager->flush();

            $user = $notifUser->getUser();
            // on desactive les notifs pour un utilisateur
            if ($user) {
                $notifsForUser = $this->entityManager->getRepository(Model\NotificationUser::class)->findBy(['user_id' => $user->getId()]);
                if ($notifsForUser) {
                    foreach ($notifsForUser as $notifForUser) {
                        $notifForUser->setActive(false);
                        $this->entityManager->flush();
                    }
                }
            }

            // on desactive les notifs si une adresse mail à plusieurs notifs
            $mail = $notifUser->getMail();
            if ($mail) {
                $notifsForMail = $this->entityManager->getRepository(Model\NotificationUser::class)->findBy(['mail' => $mail]);
                if ($notifsForMail) {
                    foreach ($notifsForMail as $notifForMail) {
                        $notifForMail->setActive(false);
                        $this->entityManager->flush();
                    }
                }
            }
        }

        return $this->redirectToRoute('login');
    }
}
