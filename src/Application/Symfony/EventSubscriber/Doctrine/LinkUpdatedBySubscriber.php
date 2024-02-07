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
use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;

/**
 * Class LinkUpdatedBySubscriber
 * Link authenticated user to object.
 */
class LinkUpdatedBySubscriber implements EventSubscriber
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var bool
     */
    private $linkAdmin;

    public function __construct(
        UserProvider $userProvider,
        bool $linkAdmin
    ) {
        $this->userProvider = $userProvider;
        $this->linkAdmin    = $linkAdmin;
    }

    public function getSubscribedEvents()
    {
        return [
            'preUpdate',
        ];
    }

    /**
     * PrePersist.
     *
     * Link creator to object.
     * That is to say that every time you will persist an object,
     * the user will be added to related object.
     *
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $em      = $args->getEntityManager();
        $object  = $args->getObject();
        $methods = \get_class_methods($object);

        $token = $this->userProvider->getToken();
        $user  = $token ? $token->getUser() : null;

        // Only link if prerequisite are right :
        // - Model has required methods
        // - Connected user is a User Model (i.e. avoid anonymous users)
        if (
            !\in_array('setUpdatedBy', $methods)
            || !\in_array('getUpdatedBy', $methods)
            || !$user instanceof User
        ) {
            return;
        }

        // No need to link admin, then link logged user (even if it is an impersonated one)
        if (!$this->linkAdmin) {
            $object->setUpdatedBy($user->getFullName());

            return;
        }

        // We link admin, then check it original token
        if ($token instanceof SwitchUserToken) {
            /** @var User $originalUser */
            $originalUser   = $token->getOriginalToken()->getUser();
            $originalUserId = $originalUser->getId()->toString();
            $originalUser   = $em->find(User::class, $originalUserId);
            $object->setUpdatedBy($originalUser->getFullName());

            return;
        }

        // Can't link admin, then link standard user
        $object->setUpdatedBy($user->getFullName());
    }
}
