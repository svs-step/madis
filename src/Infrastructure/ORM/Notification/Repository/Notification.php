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
use App\Domain\User\Dictionary\UserMoreInfoDictionary;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Model\Collectivity;
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

        $allNotifs = false;

        $allowedRoles = [UserRoleDictionary::ROLE_REFERENT, UserRoleDictionary::ROLE_ADMIN];
        if ($user && (count($user->getRoles()) && in_array($user->getRoles()[0], $allowedRoles)) || in_array(UserMoreInfoDictionary::MOREINFO_DPD, $user->getMoreInfos())) {
            // Find notifications with null user if current user is dpo
            $allNotifs = true;
        }

        $qb = $this->createQueryBuilder();

        if ($allNotifs) {

            $qb->where('o.dpo = 1');
            if ($user->getRoles()[0] === UserRoleDictionary::ROLE_REFERENT) {
                $collectivityIds = $user->getCollectivitesReferees()->map(function (Collectivity $c) {return $c->getId()->__toString();})->toArray();
                $qb->innerJoin('o.collectivity', 'c');
                $qb->andWhere(
                    $qb->expr()->in('c.id', ':ids')
                );
                $qb->setParameter('ids', $collectivityIds);
            }
        } else {
            $qb->leftJoin('o.notificationUsers', 'u')
                ->where('u.active = 1')
                ->where('u.user = :user')
                ->setParameter('user', $user)
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function persist($object)
    {
        $this->getManager()->persist($object);
    }

    public function findOneBy(array $criteria)
    {
        $notifs = $this->registry
            ->getManager()
            ->getRepository($this->getModelClass())
            ->findBy($criteria)
        ;
        if (count($notifs) > 0) {
            return $notifs[0];
        }
    }
}
