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
use App\Application\Traits\RepositoryUtils;
use App\Domain\Notification\Model;
use App\Domain\Notification\Repository;
use App\Domain\User\Dictionary\UserMoreInfoDictionary;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Domain\Registry\Model\Mesurement;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class Notification extends CRUDRepository implements Repository\Notification
{
    use RepositoryUtils;

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

    private function getQueryBuilder(array $order = ['createdAt' => 'DESC']): QueryBuilder
    {
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

        $qb = $this->createQueryBuilder()
            ->addSelect('collectivity')
            ->leftJoin('o.collectivity', 'collectivity')
        ;

        if ($allNotifs) {
            $qb->where('o.dpo = 1');
            if (!in_array(UserRoleDictionary::ROLE_ADMIN, $user->getRoles())) {
                // For non admin users
                if (in_array(UserRoleDictionary::ROLE_REFERENT, $user->getRoles())) {
                    $cf = $user->getCollectivitesReferees();
                    $cf = new ArrayCollection([...$cf]);
                    //                    if (!is_object($cf) || ArrayCollection::class !== get_class($cf)) {
                    //                        $cf = new ArrayCollection([...$cf]);
                    //                    }
                    $collectivityIds = $cf->map(function (Collectivity $c) {return $c->getId()->__toString(); })->toArray();
                } else {
                    $collectivityIds = [$user->getCollectivity()->getId()->__toString(), null];
                }
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

        return $qb;
    }

    public function findAll(array $order = ['createdAt' => 'DESC']): array
    {
        $qb = $this->getQueryBuilder($order);

        foreach ($order as $field => $direction) {
            $qb->addOrderBy(new OrderBy('o.' . $field, $direction));
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

    public function count(array $criteria = [])
    {
        $qb = $this->getQueryBuilder();

        $qb->select('COUNT(o.id)');

        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
            $collectivityIds = $criteria['collectivity']->toArray();
            array_push($collectivityIds, null);
            // $qb->innerJoin('o.collectivity', 'collectivite');
            $qb->andWhere(
                $qb->expr()->in('collectivity', ':collectivities')
            )
                ->setParameter('collectivities', $collectivityIds)
            ;
            unset($criteria['collectivity']);
        }
        //
        //        dd($criteria);

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        $qb = $this->getQueryBuilder();

        //        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
        //            $collectivityIds = $criteria['collectivity']->map(function($c) {
        //                if ($c instanceof Collectivity) {
        //                    return $c->getId()->__toString();
        //                }
        //                return $c;
        //            })->toArray();
        //            //dd($collectivityIds);
        //            array_push($collectivityIds, null);
        //            //$qb->innerJoin('o.collectivity', 'collectivite');
        //            $qb->andWhere(
        //                $qb->expr()->in('collectivity', ':collectivities')
        //            )
        //                ->setParameter('collectivities', $collectivityIds)
        //            ;
        //
        //            //dd($qb->getQuery()->getParameters());
        unset($criteria['collectivity']);
        //        }

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        $this->addTableOrder($qb, $orderColumn, $orderDir);
        $this->addTableSearches($qb, $searches);

        $qb = $qb->getQuery();
        $qb->setFirstResult($firstResult);
        $qb->setMaxResults($maxResults);

        return new Paginator($qb);
    }

    private function addTableSearches(QueryBuilder $queryBuilder, $searches)
    {
        $user = $this->security->getUser();
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'state':
                    if ('read' === $search) {
                        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_REFERENT')) {
                            $queryBuilder->andWhere('o.readAt IS NOT NULL');
                        } else {
                            $queryBuilder->leftJoin('o.notificationUsers', 'nu');
                            $queryBuilder->andWhere('nu.user=:user');
                            $queryBuilder->andWhere('nu.readAt IS NOT NULL');
                            $queryBuilder->setParameter('user', $user);
                        }
                    } else {
                        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_REFERENT')) {
                            $queryBuilder->andWhere('o.readAt IS NULL');
                        } else {
                            $queryBuilder->leftJoin('o.notificationUsers', 'nu');
                            $queryBuilder->andWhere('nu.user=:user');
                            $queryBuilder->andWhere('nu.readAt IS NULL');
                            $queryBuilder->setParameter('user', $user);
                        }
                    }

                    break;
                case 'collectivity':
                    $queryBuilder->andWhere('collectivity.name LIKE :nom')
                        ->setParameter('nom', '%' . $search . '%');
                    break;
                case 'name':
                    $queryBuilder->andWhere('o.name LIKE :name')
                        ->setParameter('name', '%' . $search . '%');
                    break;
                case 'action':
                    if ('automatic' === $search) {
                        $queryBuilder
                            ->andWhere('o.action LIKE :a1 OR o.action LIKE :a2 OR o.action LIKE :a3')
                            ->setParameter('a1', '%no_login')
                            ->setParameter('a2', '%late_survey')
                            ->setParameter('a3', '%late_action')
                        ;
                    } else {
                        $queryBuilder->andWhere('o.action LIKE :action')
                            ->setParameter('action', '%' . $search . '%');
                    }

                    break;
                case 'module':
                    $queryBuilder->andWhere('o.module LIKE :module')
                        ->setParameter('module', '%' . $search . '%');
                    break;
                case 'object':
                    $queryBuilder->andWhere('o.subject LIKE :object')
                        ->setParameter('object', '%' . $search . '%');
                    break;
                case 'date':
                    $queryBuilder->andWhere('o.createdAt LIKE :createdAt')
                        ->setParameter('createdAt', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
                case 'user':
                    $queryBuilder->leftJoin('o.createdBy', 'cb');
                    $queryBuilder->andWhere('CONCAT(cb.firstName, \' \', cb.lastName) LIKE :user')
                        ->setParameter('user', '%' . $search . '%');
                    break;
                case 'read_date':
                    if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_REFERENT')) {
                        $queryBuilder->andWhere('o.readAt LIKE :readAt')
                            ->setParameter('readAt', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    } else {
                        $queryBuilder->leftJoin('o.notificationUsers', 'nu2');
                        $queryBuilder->andWhere('nu2.user=:user');
                        $queryBuilder->andWhere('nu2.readAt LIKE :readAt')
                            ->setParameter('readAt', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                        $queryBuilder->setParameter('user', $user);
                    }
                    break;
                case 'updatedAt':
                    $queryBuilder->andWhere('o.updatedAt LIKE :updatedAt')
                        ->setParameter('updatedAt', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
            }
        }
    }

    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'read_date':
            case 'state':
                if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_REFERENT')) {
                    $queryBuilder->addOrderBy('o.readAt', $orderDir);
                } else {
                    $user = $this->security->getUser();
                    $queryBuilder->leftJoin('o.notificationUsers', 'nu3');
                    $queryBuilder->andWhere('nu3.user=:user');
                    $queryBuilder->addOrderBy('nu3.readAt');
                    $queryBuilder->setParameter('user', $user);
                }
                break;
            case 'name':
                $queryBuilder->addOrderBy('o.name', $orderDir);
                break;
            case 'collectivity':
                $queryBuilder->addOrderBy('collectivity.name', $orderDir);
                break;
            case 'action':
                $queryBuilder->addOrderBy('o.action', $orderDir);
                break;
            case 'object':
                $queryBuilder->addOrderBy('o.subject', $orderDir);
                break;
            case 'module':
                $queryBuilder->addOrderBy('o.module', $orderDir);
                break;
            case 'date':
                $queryBuilder->addOrderBy('o.createdAt', $orderDir);
                break;
            case 'user':
                $queryBuilder->leftJoin('o.createdBy', 'cb');
                $queryBuilder->addSelect('CONCAT(cb.firstName, \' \', cb.lastName)
                    AS HIDDEN person_name')
                    ->addOrderBy('person_name', $orderDir);
                break;
            case 'updatedAt':
                $queryBuilder->addOrderBy('o.updatedAt', $orderDir);
                break;
        }
    }

    public function objectExists(\App\Domain\Notification\Model\Notification $notification): bool
    {
        if (!$notification->getObject()) {
            return false;
        }

        $object = $notification->getObject();
        $moduleName = str_replace('notification.modules.', '', $notification->getModule());

        if ($moduleName === 'action_plan') {
            $objectClass = Mesurement::class;
        } else {
            $objectClass = array_flip(\App\Domain\Notification\Model\Notification::MODULES)[$moduleName];
        }


        return (bool) $this->registry->getRepository($objectClass)->find($object->id);
    }
}
