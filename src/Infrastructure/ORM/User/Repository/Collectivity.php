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

namespace App\Infrastructure\ORM\User\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Application\Traits\RepositoryUtils;
use App\Domain\User\Dictionary\CollectivityTypeDictionary;
use App\Domain\User\Model;
use App\Domain\User\Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Collectivity extends CRUDRepository implements Repository\Collectivity
{
    use RepositoryUtils;

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Collectivity::class;
    }

    /**
     * {@inheritdoc}
     */
    public function findByIds(array $ids): array
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->andWhere($qb->expr()->in('o.id', ':in_ids'))
            ->setParameter('in_ids', $ids)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByTypes(array $types, ?Model\Collectivity $excludedCollectivity = null): array
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->andWhere($qb->expr()->in('o.type', ':in_types'))
            ->setParameter('in_types', $types)
        ;

        if (null !== $excludedCollectivity) {
            $qb
                ->andWhere($qb->expr()->neq('o.id', ':excluded_collectivity_id'))
                ->setParameter('excluded_collectivity_id', $excludedCollectivity->getId()->toString())
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAllActive(bool $active = true, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere('o.active = :active')
           ->setParameter('active', $active)
        ;

        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function count(array $criteria = [])
    {
        $qb = $this
            ->createQueryBuilder()
            ->select('count(o.id)')
        ;

        if (\array_key_exists('collectivitesReferees', $criteria)) {
            $qb
                ->andWhere($qb->expr()->in('o.id', ':collectivitesReferees'))
                ->setParameter('collectivitesReferees', $criteria['collectivitesReferees']);
            unset($criteria['collectivitesReferees']);
        }

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        return $qb
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        if (\array_key_exists('collectivitesReferees', $criteria)) {
            $qb
                ->andWhere($qb->expr()->in('o.id', ':collectivitesReferees'))
                ->setParameter('collectivitesReferees', $criteria['collectivitesReferees']);
            unset($criteria['collectivitesReferees']);
        }

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        $this->addTableOrder($qb, $orderColumn, $orderDir);
        $this->addTableWhere($qb, $searches);

        $query = $qb->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($maxResults);

        return new Paginator($query);
    }

    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'nom':
                $queryBuilder->addOrderBy('o.name', $orderDir);
                break;
            case 'nom_court':
                $queryBuilder->addOrderBy('o.shortName', $orderDir);
                break;
            case 'type':
                $queryBuilder->addSelect('(case
                WHEN o.type = \'' . CollectivityTypeDictionary::TYPE_OTHER . '\' THEN 1
                WHEN o.type = \'' . CollectivityTypeDictionary::TYPE_SOCIAL_INSTITUTION . '\' THEN 2
                WHEN o.type = \'' . CollectivityTypeDictionary::TYPE_MEDICAL_INSTITUTION . '\' THEN 3
                WHEN o.type = \'' . CollectivityTypeDictionary::TYPE_COMMUNE . '\' THEN 4
                WHEN o.type = \'' . CollectivityTypeDictionary::TYPE_EPCI . '\' THEN 5
                WHEN o.type = \'' . CollectivityTypeDictionary::TYPE_DEPARTMENTAL_UNION . '\' THEN 6
                WHEN o.type = \'' . CollectivityTypeDictionary::TYPE_SANITARY_INSTITUTION . '\' THEN 7
                WHEN o.type = \'' . CollectivityTypeDictionary::TYPE_ENTERPRISE . '\' THEN 8
                WHEN o.type = \'' . CollectivityTypeDictionary::TYPE_MODEL . '\' THEN 9
                ELSE 10 END) AS HIDDEN hidden_type')
                    ->addOrderBy('hidden_type', $orderDir);
                break;
            case 'siren':
                $queryBuilder->addOrderBy('o.siren', $orderDir);
                break;
            case 'statut':
                $queryBuilder->addOrderBy('o.active', $orderDir);
                break;
        }
    }

    private function addTableWhere(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'nom':
                    $this->addWhereClause($queryBuilder, 'name', '%' . $search . '%', 'LIKE');
                    break;
                case 'nom_court':
                    $this->addWhereClause($queryBuilder, 'shortName', '%' . $search . '%', 'LIKE');
                    break;
                case 'type':
                    $this->addWhereClause($queryBuilder, 'type', $search);
                    break;
                case 'siren':
                    $this->addWhereClause($queryBuilder, 'siren', '%' . $search . '%', 'LIKE');
                    break;
                case 'statut':
                    $this->addWhereClause($queryBuilder, 'active', $search);
                    break;
            }
        }
    }

    public function findByUserReferent(Model\User $userReferent, bool $active = true)
    {
        return $this->createQueryBuilder()
            ->leftJoin('o.userReferents', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $userReferent)
            ->andWhere('o.active = :active')
            ->setParameter('active', $active)
            ->getQuery()
            ->getResult()
        ;
    }
}
