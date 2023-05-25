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

namespace App\Infrastructure\ORM\Notification\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Notification\Model;
use App\Domain\Notification\Repository;
use Doctrine\ORM\QueryBuilder;

class NotificationUser extends CRUDRepository implements Repository\NotificationUser
{
    protected function getModelClass(): string
    {
        return Model\NotificationUser::class;
    }

    public function saveUsers(Model\Notification $notification, $users): array
    {
        $nus = [];
        foreach ($users as $user) {
            $nu = $this->create();
            $nu->setUser($user);
            $nu->setNotification($notification);
            $nu->setToken(sha1($user->getId() . microtime() . mt_rand()));
            $nu->setActive(true);
            $nu->setSent(false);
            $nus[] = $nu;
        }

        return $nus;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder();
    }

    public function persist(Model\NotificationUser $object): void
    {
        $this->getManager()->persist($object);
    }
}
