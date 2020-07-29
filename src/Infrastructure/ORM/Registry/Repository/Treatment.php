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

namespace App\Infrastructure\ORM\Registry\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Treatment extends CRUDRepository implements Repository\Treatment
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Treatment::class;
    }

    /**
     * Add collectivity clause to query.
     */
    protected function addCollectivityClause(QueryBuilder $qb, Collectivity $collectivity): QueryBuilder
    {
        return $qb
            ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
            ;
    }

    /**
     * Add active clause to query.
     */
    protected function addActiveClause(QueryBuilder $qb, bool $active = true): QueryBuilder
    {
        return $qb
            ->andWhere('o.active = :active')
            ->setParameter('active', $active)
            ;
    }

    /**
     * Add order to query.
     */
    protected function addOrder(QueryBuilder $qb, array $order = []): QueryBuilder
    {
        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllActive(bool $active = true, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        $this->addActiveClause($qb, $active);
        $this->addOrder($qb, $order);

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        $this->addCollectivityClause($qb, $collectivity);
        $this->addOrder($qb, $order);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllActiveByCollectivity(Collectivity $collectivity = null, bool $active = true, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        if (!\is_null($collectivity)) {
            $this->addCollectivityClause($qb, $collectivity);
        }
        $this->addActiveClause($qb, $active);
        $this->addOrder($qb, $order);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function countAllByCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');
        $this->addCollectivityClause($qb, $collectivity);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOrNullLastUpdateByCollectivity(Collectivity $collectivity): ?Model\Treatment
    {
        $qb = $this->createQueryBuilder();

        $this->addCollectivityClause($qb, $collectivity);
        $qb->addOrderBy('o.updatedAt', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countAllActiveByCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');
        $this->addCollectivityClause($qb, $collectivity);
        $this->addActiveClause($qb, true);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByActiveCollectivity(bool $active = true)
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('o.collectivity', 'c')
            ->andWhere($qb->expr()->eq('c.active', ':active'))
            ->setParameter('active', $active)
            ->addOrderBy('c.name')
            ->addOrderBy('o.createdAt', 'DESC')
        ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllActiveByCollectivityWithHasModuleConformiteTraitement(Collectivity $collectivity = null, bool $active = true, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        if (!\is_null($collectivity)) {
            $this->addCollectivityClause($qb, $collectivity);
        }
        $this->addActiveClause($qb, $active);
        $this->addOrder($qb, $order);

        $qb->leftJoin('o.collectivity', 'c')
            ->andWhere($qb->expr()->eq('c.hasModuleConformiteTraitement', ':active'))
            ->setParameter('active', true)
        ;

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function countAllWithNoConformiteTraitementByCollectivity(?Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');
        $this->addCollectivityClause($qb, $collectivity);
        $this->addActiveClause($qb, true);
        $qb->leftJoin('o.conformiteTraitement', 'cT')
            ->andWhere($qb->expr()->isNull('cT.id'))
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function count(array $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');
        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('collectivite')
            ->leftJoin('o.collectivity', 'collectivite')
        ;

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        $this->addTableOrder($qb, $orderColumn, $orderDir);
//        $this->addTableSearches($qb, $searches);

        $qb = $qb->getQuery();
        $qb->setFirstResult($firstResult);
        $qb->setMaxResults($maxResults);

        return new Paginator($qb);
    }

    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'name':
                $queryBuilder->addOrderBy('o.name', $orderDir);
                break;
            case 'collectivite':
                $queryBuilder->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'baseLegal':
                $queryBuilder->addOrderBy('o.legalBasis', $orderDir);
                break;
            case 'logiciel':
                $queryBuilder->addOrderBy('o.software', $orderDir);
                break;
            case 'enTantQue':
                $queryBuilder->addOrderBy('o.author', $orderDir);
                break;
            case 'gestionnaire':
                $queryBuilder->addOrderBy('o.manager', $orderDir);
                break;
            case 'controleAcces':
                $queryBuilder->addOrderBy('o.securityAccessControl.check', $orderDir);
                break;
            case 'tracabilite':
                $queryBuilder->addOrderBy('o.securityTracability.check', $orderDir);
                break;
            case 'saving':
                $queryBuilder->addOrderBy('o.securitySaving.check', $orderDir);
                break;
            case 'update':
                $queryBuilder->addOrderBy('o.securityUpdate.check', $orderDir);
                break;
            case 'other':
                $queryBuilder->addOrderBy('o.securityOther.check', $orderDir);
                break;
            case 'entitledPersons':
                $queryBuilder->addOrderBy('o.securityEntitledPersons', $orderDir);
                break;
            case 'openAccounts':
                $queryBuilder->addOrderBy('o.securityOpenAccounts', $orderDir);
                break;
            case 'specificitiesDelivered':
                $queryBuilder->addOrderBy('o.securitySpecificitiesDelivered', $orderDir);
                break;
        }
    }

    /**
     * Add a where clause to query.
     *
     * @param mixed $value
     */
    protected function addWhereClause(QueryBuilder $qb, string $key, $value): QueryBuilder
    {
        return $qb
            ->andWhere("o.{$key} = :{$key}_value")
            ->setParameter("{$key}_value", $value)
            ;
    }
}
