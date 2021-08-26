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
use App\Application\Traits\RepositoryUtils;
use App\Domain\Registry\Dictionary\MesurementPriorityDictionary;
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class Mesurement extends CRUDRepository implements Repository\Mesurement
{
    use RepositoryUtils;

    /**
     * @var Security
     */
    private $security;

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
        return Model\Mesurement::class;
    }

    /**
     * Add a collectivity appartenance clause.
     */
    protected function addCollectivityClause(QueryBuilder $qb, Collectivity $collectivity): QueryBuilder
    {
        return $qb
            ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
        ;
    }

    /**
     * Add an order to query.
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
    public function findBy(array $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByPlanified(array $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }
        $qb->andWhere('o.planificationDate is not null');

        $qb->orderBy('o.planificationDate', 'ASC');

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function countPlanifiedByCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');
        $qb->andWhere($qb->expr()->isNotNull('o.planificationDate'));
        $qb->andWhere($qb->expr()->eq('o.collectivity', ':collectivity'));
        $qb->andWhere($qb->expr()->neq('o.status', ':status'));
        $qb->setParameters([
            'status'       => MesurementStatusDictionary::STATUS_APPLIED,
            'collectivity' => $collectivity,
        ]);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countAppliedByCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');
        $qb->andWhere($qb->expr()->eq('o.collectivity', ':collectivity'));
        $qb->andWhere($qb->expr()->eq('o.status', ':status'));
        $qb->setParameters([
            'status'       => MesurementStatusDictionary::STATUS_APPLIED,
            'collectivity' => $collectivity,
        ]);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function planifiedAverageOnAllCollectivity($collectivities)
    {
        $sql = 'SELECT AVG(a.rcount) FROM (
            SELECT COUNT(rm.id) as rcount
            FROM user_collectivity uc
            LEFT OUTER JOIN registry_mesurement rm ON (uc.id = rm.collectivity_id AND rm.planification_date is not null
            AND rm.status = "applied" )
            WHERE uc.active = 1';

        if (!empty($collectivities)) {
            $sql .= ' AND uc.id IN (';
            $sql .= \implode(',', \array_map(function ($collectivity) {
                return '\'' . $collectivity->getId() . '\'';
            }, $collectivities));
            $sql .= ') ';
        }

        $sql .= ' GROUP BY uc.id
        ) a';

        $stmt = $this->getManager()->getConnection()->prepare($sql);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function count(array $criteria = [])
    {
        $qb = $this
            ->createQueryBuilder()
            ->select('count(o.id)')
        ;

        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
            $qb->leftJoin('o.collectivity', 'collectivite');
            $this->addInClauseCollectivities($qb, $criteria['collectivity']->toArray());
            unset($criteria['collectivity']);
        }

        if (isset($criteria['planificationDate']) && 'null' === $criteria['planificationDate']) {
            $qb->andWhere($qb->expr()->isNotNull('o.planificationDate'));
            unset($criteria['planificationDate']);
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
        $query = $this->createQueryBuilder();

        $query->leftJoin('o.collectivity', 'collectivite')
            ->addSelect('collectivite');

        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
            $this->addInClauseCollectivities($query, $criteria['collectivity']->toArray());
            unset($criteria['collectivity']);
        }

        if (isset($criteria['planificationDate']) && 'null' === $criteria['planificationDate']) {
            $query->andWhere($query->expr()->isNotNull('o.planificationDate'));
            unset($criteria['planificationDate']);
        }

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($query, $key, $value);
        }
        $this->addTableWhere($query, $searches);
        $this->addTableOrder($query, $orderColumn, $orderDir);

        $query = $query->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($maxResults);

        return new Paginator($query);
    }

    private function addTableWhere(QueryBuilder $queryBuilder, array $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'nom':
                    $this->addWhereClause($queryBuilder, 'name', '%' . $search . '%', 'LIKE');
                    break;
                case 'collectivite':
                    $queryBuilder->andWhere('collectivite.name LIKE :collectivite')
                        ->setParameter('collectivite', '%' . $search . '%');
                    break;
                case 'statut':
                    $this->addWhereClause($queryBuilder, 'status', $search);
                    break;
                case 'cout':
                    $this->addWhereClause($queryBuilder, 'cost', '%' . $search . '%', 'LIKE');
                    break;
                case 'charge':
                    $this->addWhereClause($queryBuilder, 'charge', '%' . $search . '%', 'LIKE');
                    break;
                case 'priorite':
                    $this->addWhereClause($queryBuilder, 'priority', $search);
                    break;
                case 'date_planification':
                    $queryBuilder->andWhere('o.planificationDate LIKE :date')
                        ->setParameter('date', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
                case 'responsable_action':
                    $this->addWhereClause($queryBuilder, 'manager', '%' . $search . '%', 'LIKE');
                    break;
            }
        }
    }

    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'nom':
                $queryBuilder->addOrderBy('o.name', $orderDir);
                break;
            case 'statut':
                $queryBuilder->addOrderBy('o.status', $orderDir);
                break;
            case 'cout':
                $queryBuilder->addOrderBy('o.cost', $orderDir);
                break;
            case 'charge':
                $queryBuilder->addOrderBy('o.charge', $orderDir);
                break;
            case 'collectivite':
                $queryBuilder->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'priorite':
                $queryBuilder->addSelect('(case
                WHEN o.priority = \'' . MesurementPriorityDictionary::PRIORITY_LOW . '\' THEN 1
                WHEN o.priority = \'' . MesurementPriorityDictionary::PRIORITY_NORMAL . '\' THEN 2
                WHEN o.priority = \'' . MesurementPriorityDictionary::PRIORITY_HIGH . '\' THEN 3
                ELSE 4 END) AS HIDDEN hidden_priority')
                    ->addOrderBy('hidden_priority', $orderDir);
                break;
            case 'date_planification':
                $queryBuilder->addOrderBy('o.planificationDate', $orderDir);
                break;
            case 'responsable_action':
                $queryBuilder->addOrderBy('o.manager', $orderDir);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByActiveCollectivity(bool $active = true, User $user = null)
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('o.collectivity', 'c')
            ->andWhere($qb->expr()->eq('c.active', ':active'))
            ->setParameter('active', $active)
            ->addOrderBy('c.name')
            ->addOrderBy('o.createdAt', 'DESC')
        ;

        if (null !== $user) {
            $qb->leftJoin('c.userReferents', 'u')
                ->andWhere('u.id = :user')
                ->setParameter('user', $user);
        }

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllByClonedFromCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('o.clonedFrom', 'c')
            ->andWhere('c.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity);

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
