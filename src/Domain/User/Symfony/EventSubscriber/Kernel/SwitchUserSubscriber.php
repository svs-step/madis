<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Domain\User\Symfony\EventSubscriber\Kernel;

use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SwitchUserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security      = $security;
        $this->entityManager = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::SWITCH_USER => 'onSwitchUser',
        ];
    }

    public function onSwitchUser(SwitchUserEvent $event)
    {
        $request    = $event->getRequest();
        $switchUser = $request->get('_switch_user');

        /** @var User $user */
        $user   = $this->security->getUser();
        $action = LogJournalActionDictionary::SWITCH_USER_ON;
        /** @var User $targetUser */
        $targetUser         = $event->getTargetUser();
        $switchUserFullName = $targetUser->getFullName();

        if ('_exit' === $switchUser) {
            $action             = LogJournalActionDictionary::SWITCH_USER_OFF;
            $switchUserFullName = $user->getFullName();
            /** @var User $user */
            $user = $event->getTargetUser();
        }

        $log = new LogJournal(
            $user->getCollectivity(),
            $user->getFullName(),
            $user->getEmail(),
            $action,
            LogJournalSubjectDictionary::USER_USER,
            $user->getId()->toString(),
            $switchUserFullName
        );

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
