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

namespace App\Domain\Reporting\Symfony\EventSubscriber\Doctrine;

use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\Reporting\Symfony\EventSubscriber\Event\LogJournalEvent;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;

class LogJournalDoctrineSubscriber implements EventSubscriber
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        Security $security,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager
    ) {
        $this->security        = $security;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager   = $entityManager;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::preRemove,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        if (!$this->supports($args->getObject())) {
            return;
        }

        $this->registerLog($args, LogJournalActionDictionary::CREATE);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        if (!$this->supports($args->getObject())) {
            return;
        }

        //specific case for user. Need to know which data is update
        if ($args->getObject() instanceof User) {
            $this->registerLogForUser($args);
        } else {
            $this->registerLog($args, LogJournalActionDictionary::UPDATE);
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        if (!$this->supports($args->getObject())) {
            return;
        }

        $this->registerDeleteLog($args);
    }

    private function registerLog(LifecycleEventArgs $args, string $action)
    {
        $object  = $args->getObject();
        $user    = $this->security->getUser();
        $subject = LogJournalSubjectDictionary::getSubjectFromClassName(\get_class($object));

        $log = new LogJournal($this->getCollectivity($object), $user, $action, $subject, $object);
        $this->eventDispatcher->dispatch(new LogJournalEvent($log));
    }

    private function registerDeleteLog(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $user   = $this->security->getUser();

        $subject = LogJournalSubjectDictionary::getSubjectFromClassName(\get_class($object));

        $log = new LogJournal(
            $this->getCollectivity($object),
            $user,
            LogJournalActionDictionary::DELETE,
            $subject,
            null,
            $object->__toString() . '-' . $object->getId()->__toString()
        );

        $this->eventDispatcher->dispatch(new LogJournalEvent($log, $object));
    }

    private function supports($object): bool
    {
        return $object instanceof LoggableSubject && !\is_null($this->security->getUser());
    }

    private function getCollectivity($object)
    {
        if (Collectivity::class === \get_class($object)) {
            return $object;
        }

        if (\method_exists($object, 'getCollectivity')) {
            return $object->getCollectivity();
        }

        /** @var User $user */
        $user = $this->security->getUser();

        return $user->getCollectivity();
    }

    private function registerLogForUser(LifecycleEventArgs $args)
    {
        $object  = $args->getObject();
        $user    = $this->security->getUser();
        $changes = $this->entityManager->getUnitOfWork()->getEntityChangeSet($object);

        //don't need to add log on lastLogin because already register in LoginSubscriber
        if (\array_key_exists('lastlogin', $changes)) {
            return;
        }

        $actions      = [];
        $collectivity = $this->getCollectivity($object);
        if (\array_key_exists('firstName', $changes)) {
            $actions[] =  LogJournalSubjectDictionary::USER_FIRSTNAME;
        }

        if (\array_key_exists('lastName', $changes)) {
            $actions[] =  LogJournalSubjectDictionary::USER_LASTNAME;
        }

        if (\array_key_exists('email', $changes)) {
            $actions[] =  LogJournalSubjectDictionary::USER_EMAIL;
        }

        if (\array_key_exists('password', $changes)) {
            $actions[] =  LogJournalSubjectDictionary::USER_PASSWORD;
        }

        foreach ($actions as $action) {
            $log = new LogJournal($collectivity, $user, $action, $action, $object);
            $this->eventDispatcher->dispatch(new LogJournalEvent($log));
        }
    }
}
