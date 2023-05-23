<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
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

namespace App\Application\Symfony\EventSubscriber\Kernel;

use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\Reporting\Repository\LogJournal as LogRepository;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LogRepository
     */
    private $logJournalRepository;

    /**
     * @var string
     */
    private $logJournalDuration;

    /**
     * @var Security
     */
    private $security;

    public function __construct(
        EntityManagerInterface $em,
        LogRepository $logRepository,
        Security $security,
        string $logJournalDuration
    ) {
        $this->entityManager        = $em;
        $this->logJournalRepository = $logRepository;
        $this->security             = $security;
        $this->logJournalDuration   = $logJournalDuration;
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        $user->setLastLogin(new \DateTimeImmutable());
        $this->entityManager->persist($user);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $this->logJournalRepository->deleteAllAnteriorToDate(new \DateTime('-' . $this->logJournalDuration));
        }

        $log = new LogJournal(
            $user->getCollectivity(),
            $user->getFullName(),
            $user->getEmail(),
            LogJournalActionDictionary::LOGIN,
            LogJournalSubjectDictionary::USER_USER,
            $user->getId()->toString(),
            $user->getFullName()
        );

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
