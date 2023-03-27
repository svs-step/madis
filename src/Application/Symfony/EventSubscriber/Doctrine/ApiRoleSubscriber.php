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

use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ApiRoleSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * PrePersist
     * Add API_ROLE to User.
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }
        $this->setRoleApiAuthorized($user);
    }

    public function setRoleApiAuthorized(User $user): void
    {
        $isApiAuthorized = $user->getApiAuthorized();

        if (null == $isApiAuthorized) {
            return;
        }

        $roles = $user->getRoles();

        if ($isApiAuthorized) {
            $roles[] = 'ROLE_API';
        } else {
            $roles = array_diff($roles, ['API_ROLE']);
        }

        $user->setRoles($roles);
    }

    /**
     * PrePersist
     * Add API_ROLE to User.
     *
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        if (!$args->hasChangedField('apiAuthorized')) {
            return;
        }

        $this->setRoleApiAuthorized($user);
    }
}
