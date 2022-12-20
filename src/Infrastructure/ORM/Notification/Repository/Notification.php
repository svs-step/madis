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
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Model\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class Notification extends CRUDRepository implements Repository\Notification
{
    protected Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry);
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Notification::class;
    }

    public function findAll(array $order = ['createdAt' => 'desc']): array
    {
        // TODO only get notifications for the current user.
        $orderBy = [];
        foreach ($order as $key => $value) {
            $orderBy[$key] = $value;
        }
        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        $allowedRoles = [UserRoleDictionary::ROLE_REFERENT, UserRoleDictionary::ROLE_ADMIN];
        if ($user && count($user->getRoles()) && in_array($user->getRoles()[0], $allowedRoles)) {
            // Find notifications with null user if current user is dpo
            $user = null;
        }

        $qb = $this->createQueryBuilder();

        $qb->select('n')
            ->from($this->getModelClass(), 'n');

        if ($user) {
            $qb->leftJoin('n.notificationUsers', 'u')
                ->where('u.user = :user')
                ->setParameter('user', $user)
            ;
        } else {
            $qb->leftJoin('n.notificationUsers', 'u')
                ->having('count(u.id) = 0')
                ->groupBy('n.id')
            ;
        }

//        if (count($order)) {
//            $qb->addOrderBy(array_keys($order)[0] . ' ' . $order[0]);
//        }
        return $qb->getQuery()->getResult();
    }

    public function persist($object): void
    {
        $this->getManager()->persist($object);
    }
}
