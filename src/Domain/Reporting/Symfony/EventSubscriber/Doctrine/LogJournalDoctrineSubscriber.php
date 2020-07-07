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

use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Participant;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Model\LoggableSubject;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\Reporting\Symfony\EventSubscriber\Event\LogJournalEvent;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\ComiteIlContact;
use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
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

    /**
     * To avoid multiple line on same object register objects in folder.
     *
     * @var AdapterInterface
     */
    private $cacheAdapter;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        Security $security,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        AdapterInterface $cacheAdapter,
        RequestStack $requestStack
    ) {
        $this->security        = $security;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager   = $entityManager;
        $this->cacheAdapter    = $cacheAdapter;
        $this->requestStack    = $requestStack;
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
        $object = $args->getObject();
        if (!$this->supports($object)) {
            return;
        }

        $action = LogJournalActionDictionary::CREATE;

        switch (\get_class($args->getObject())) {
            //l'ajout d'un participant ou d'une conformité entraine la modification de l'évaluation
            case Conformite::class:
            case Participant::class:
                $object = $object->getEvaluation();
                $action = LogJournalActionDictionary::UPDATE;
                break;
            //l'ajout d'un contact IL entraine la modification de la collectivité
            case ComiteIlContact::class:
                $object = $object->getCollectivity();
                $action = LogJournalActionDictionary::UPDATE;
                break;
            //l'ajout d'une réponse entraine la modification de la conformité du traitement
            case Reponse::class:
                $object = $object->getConformiteTraitement();
                $action = LogJournalActionDictionary::UPDATE;
                break;
        }

        $this->registerLog($object, $action);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if (!$this->supports($object)) {
            return;
        }

        //specific case for user. Need to know which data is update
        switch (\get_class($args->getObject())) {
            case User::class:
                $this->registerLogForUser($object);
                break;
            case Conformite::class:
            case Participant::class:
                $this->registerLog($object->getEvaluation(), LogJournalActionDictionary::UPDATE);
                break;
            case ComiteIlContact::class:
                $this->registerLog($object->getCollectivity(), LogJournalActionDictionary::UPDATE);
                break;
            case Reponse::class:
                /** @var User $user */
                $user       = $this->security->getUser();
                $item       = $this->cacheAdapter->getItem('already_register_item-' . $user->getId()->toString());
                $request    = $this->requestStack->getCurrentRequest();
                $conformite = $object->getConformiteTraitement();
                $id         = $request->get('id');
                //Cas spéciale lors de l'édition du traitement lors de la modification d'une conformité d'un traitement
                if (\is_null($item->get()) || $id === $item->get()->toString() && $conformite->getId()->toString() === $id) {
                    $this->registerLog($conformite, LogJournalActionDictionary::UPDATE);
                }
                break;
            default:
                $this->registerLog($object, LogJournalActionDictionary::UPDATE);
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        if (!$this->supports($args->getObject())) {
            return;
        }

        $this->registerDeleteLog($args);
    }

    private function registerLog(LoggableSubject $object, string $action)
    {
        /** @var User $user */
        $user    = $this->security->getUser();
        $item    = $this->cacheAdapter->getItem('already_register_item-' . $user->getId()->toString());
        //si la clef de l'objet existe déja alors on ajoute pas de log.
        if (!$item->isHit()) {
            $item->expiresAfter(2);
            $item->set($object->getId());
            $this->cacheAdapter->save($item);
        } elseif ($object->getId()->toString() === $item->get()->toString()) {
            return;
        }

        $subject = LogJournalSubjectDictionary::getSubjectFromClassName(\get_class($object));

        $log = new LogJournal($this->getCollectivity($object), $user, $action, $subject, $object);
        $this->eventDispatcher->dispatch(new LogJournalEvent($log));
    }

    private function registerDeleteLog(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if ($this->notConcernedByDeletionLog($object)) {
            return;
        }

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

    private function registerLogForUser(LoggableSubject $object)
    {
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

    private function notConcernedByDeletionLog(LoggableSubject $subject): bool
    {
        return \in_array(\get_class($subject), [
            Conformite::class,
            Participant::class,
            ComiteIlContact::class,
            Reponse::class,
        ]);
    }
}
