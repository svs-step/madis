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

namespace App\Infrastructure\ORM\Maturity\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use App\Application\Traits\RepositoryUtils;

class Survey extends CRUDRepository implements Repository\Survey
{
    use RepositoryUtils;
    private string $lateSurveyDelayDays;

    /**
     * CRUDRepository constructor.
     */
    public function __construct(ManagerRegistry $registry, string $lateSurveyDelayDays)
    {
        parent::__construct($registry);

        $this->lateSurveyDelayDays = $lateSurveyDelayDays;
    }

    protected function getModelClass(): string
    {
        return Model\Survey::class;
    }

    public function findAllByCollectivity(Collectivity $collectivity, array $order = [], int $limit = null, array $where = []): iterable
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
        ;

        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        foreach ($where as $key => $value) {
            if (is_int($key)) {
                $qb->andWhere($value);
            } elseif (is_string($key)) {
                $qb->andWhere("o.{$key} = :o_{$key}")
                ->setParameter("o_{$key}", $value);
            }
        }

        if (!\is_null($limit)) {
            $qb
                ->setFirstResult(0)
                ->setMaxResults($limit);
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPreviousById(string $id, int $limit = 1): iterable
    {
        return $this->createQueryBuilder()
            ->select('s')
            ->from(Model\Survey::class, 's')
            ->andWhere('o.id = :id')
            ->andWhere('o.collectivity = s.collectivity')
            ->andWhere('o.referentiel = s.referentiel')  // Referentiels must match
            ->andWhere('o.createdAt > s.createdAt')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function averageSurveyDuringLastYear(array $collectivities = [])
    {
        $sql = 'SELECT AVG(a.rcount) FROM (
            SELECT IF(COUNT(ms.id) > 0, 1, 0) as rcount
            FROM user_collectivity uc
            LEFT OUTER JOIN maturity_survey ms ON (uc.id = ms.collectivity_id AND ms.created_at >= NOW() - INTERVAL 1 YEAR)
            WHERE uc.active = 1';

        if (!empty($collectivities)) {
            $sql .= ' AND uc.id IN (';
            $sql .= \implode(',', \array_map(function ($collectivity) {
                return '\'' . $collectivity->getId() . '\'';
            }, $collectivities));
            $sql .= ') ';
        }

        $sql .= ' 
            GROUP BY uc.id
        ) a';

        $stmt = $this->getManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function findAllByCollectivities(array $collectivities, array $order = [], int $limit = null): iterable
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->andWhere(
                $qb->expr()->in('o.collectivity', ':collectivities')
            )
            ->setParameter('collectivities', $collectivities)
        ;

        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        if (!\is_null($limit)) {
            $qb
                ->setFirstResult(0)
                ->setMaxResults($limit);
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllLate(): array
    {
        $now       = new \DateTime();
        $monthsAgo = $now->sub(\DateInterval::createFromDateString($this->lateSurveyDelayDays . ' days'));

        return $this->createQueryBuilder()
            ->andWhere('o.updatedAt < :lastmonth')
            ->setParameter('lastmonth', $monthsAgo->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function count(array $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('o.collectivity', 'collectivite')
            ->addSelect('collectivite');
        $qb->leftJoin('o.referentiel', 'referentiel')
            ->addSelect('referentiel');

        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
            $this->addInClauseCollectivities($qb, $criteria['collectivity']->toArray());
            unset($criteria['collectivity']);
        }

        $this->addTableSearches($qb, $searches);
        $this->addTableOrder($qb, $orderColumn, $orderDir);

        $qb = $qb->getQuery();
        $qb->setFirstResult($firstResult);
        $qb->setMaxResults($maxResults);

        return new Paginator($qb);
    }

    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'collectivity':
                $queryBuilder->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'referentiel':
                $queryBuilder->addOrderBy('referentiel.name', $orderDir);
                break;
            case 'score':
                $queryBuilder->addOrderBy('o.score', $orderDir);
                break;
            case 'updatedAt':
                $queryBuilder->addOrderBy('o.updatedAt', $orderDir);
                break;
            case 'createdAt':
                $queryBuilder->addOrderBy('o.createdAt', $orderDir);
                break;
        }
    }

    private function addTableSearches(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'collectivity':
                    $this->addWhereClause($queryBuilder, 'collectivity', '%' . $search . '%', 'LIKE');
                    break;
                case 'referentiel':
                    $this->addWhereClause($queryBuilder, 'referentiel', '%' . $search . '%', 'LIKE');
                    break;
                case 'score':
                    $this->addWhereClause($queryBuilder, 'score', '%' . $search . '%', 'LIKE');
                    break;
                case 'updatedAt':
                    if (is_string($search)) {
                        $queryBuilder->andWhere('o.updatedAt BETWEEN :updated_start_date AND :updated_finish_date')
                            ->setParameter('updated_start_date', date_create_from_format('d/m/y', substr($search, 0, 8))->format('Y-m-d 00:00:00'))
                            ->setParameter('updated_finish_date', date_create_from_format('d/m/y', substr($search, 11, 8))->format('Y-m-d 23:59:59'));
                    }
                    break;
                case 'createdAt':
                    if (is_string($search)) {
                        $queryBuilder->andWhere('o.createdAt BETWEEN :create_start_date AND :create_finish_date')
                            ->setParameter('create_start_date', date_create_from_format('d/m/y', substr($search, 0, 8))->format('Y-m-d 00:00:00'))
                            ->setParameter('create_finish_date', date_create_from_format('d/m/y', substr($search, 11, 8))->format('Y-m-d 23:59:59'));
                    }
                    break;
            }
        }
    }
}
