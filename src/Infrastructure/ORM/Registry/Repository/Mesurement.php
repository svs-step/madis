<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Infrastructure\ORM\Registry\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
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
     * @param QueryBuilder $qb
     * @param string       $key
     * @param $value
     *
     * @return QueryBuilder
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
     *
     * @param QueryBuilder $qb
     * @param Collectivity $collectivity
     *
     * @return QueryBuilder
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
     *
     * @param QueryBuilder $qb
     * @param array        $order
     *
     * @return QueryBuilder
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
}
