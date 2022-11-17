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
use App\Domain\Notification\Model;

class NotificationUserController extends CRUDController
{
    public function __construct()
    {
        // tt
    }

    protected function getDomain(): string
    {
        return 'notificationUser';
    }

    protected function getModel(): string
    {
        return 'notificationUser';
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
        $entityManager = $this->getDoctrine()->getManager();
        $notifUser     = $this->getDoctrine()->getRepository(Model\NotificationUser::class)->find($id);

        if ($notifUser && $notifUser->getNotifId() === $notif_id) {
            $notifUser->setActive(false);
            $entityManager->flush();

            $user = $notifUser->getUserId();
            // on desactive les notifs pour un utilisateur
            if ($user) {
                $notifsForUser = $this->getDoctrine()->getRepository(Model\NotificationUser::class)->findBy(['user_id' => $user]);
                if ($notifsForUser) {
                    foreach ($notifsForUser as $notifForUser) {
                        $notifForUser->setActive(false);
                        $entityManager->flush();
                    }
                }
            }

            //on desactive les notifs si une adresse mail à plusieurs notifs
            $mail = $notifUser->getMail();
            if ($mail) {
                $notifsForMail = $this->getDoctrine()->getRepository(Model\NotificationUser::class)->findBy(['mail' => $mail]);
                if ($notifsForMail) {
                    foreach ($notifsForMail as $notifForMail) {
                        $notifForMail->setActive(false);
                        $entityManager->flush();
                    }
                }
            }
        }

        return $this->redirectToRoute('login');
    }
}
