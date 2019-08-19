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
use App\Application\Traits\Model\CreatorTrait;
use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

/**
 * Class LinkCreatorSubscriber
 * Link authenticated user to object.
 */
class LinkCreatorSubscriber implements EventSubscriber
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
            'prePersist',
        ];
    }

    /**
     * PrePersist.
     *
     * Link creator to object.
     * That is to say that every time you will persist an object,
     * the user will be added to related object.
     *
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        $uses   = \class_uses($object);
        $token  = $this->userProvider->getToken();
        $user   = $token->getUser();

        if (
            !\in_array(CreatorTrait::class, $uses)
            || !$user instanceof User
        ) {
            return;
        }

        // We don't link admin in impersonate mode, then link impersonated user
        if (false === $this->linkAdmin) {
            $object->setCreator($user);

            return;
        }

        // We link admin in impersonate mode, check existence of SwitchUserRole token role
        foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                $object->setCreator($role->getSource()->getUser());

                return;
            }
        }

        // If there is no impersonation, then link standard user
        $object->setCreator($user);
    }
}
