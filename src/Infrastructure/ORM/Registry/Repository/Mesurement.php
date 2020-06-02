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
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\ORM\QueryBuilder;

class Mesurement extends CRUDRepository implements Repository\Mesurement
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Mesurement::class;
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
    public function planifiedAverageOnAllCollectivity()
    {
        $sql = 'SELECT AVG(a.rcount) FROM (
            SELECT COUNT(rm.id) as rcount
            FROM user_collectivity uc
            LEFT OUTER JOIN registry_mesurement rm ON (uc.id = rm.collectivity_id AND rm.planification_date is not null
            AND rm.status = "applied" )
            WHERE uc.active = 1
            GROUP BY uc.id
        ) a';

        $stmt = $this->getManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
