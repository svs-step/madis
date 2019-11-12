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

namespace App\Application\Symfony\EventSubscriber\Doctrine;

use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\Model\CollectivityTrait;
use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class LinkCollectivitySubscriber
 * Link authenticated user collectivity to object.
 */
class LinkCollectivitySubscriber implements EventSubscriber
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
        ];
    }

    /**
     * PrePersist.
     *
     * Link user collectivity to persisted object.
     * That is to say that every time you will persist an object,
     * the user collectivity will be added to related object.
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        $user   = $this->userProvider->getAuthenticatedUser();
        $uses   = \class_uses($object);

        if (
            \in_array(CollectivityTrait::class, $uses)
            && $user instanceof User
            && null === $object->getCollectivity()
        ) {
            $object->setCollectivity($user->getCollectivity());
        }
    }
}
