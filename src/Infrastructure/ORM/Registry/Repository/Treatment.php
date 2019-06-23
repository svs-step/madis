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
     * Add active clause to query.
     *
     * @param QueryBuilder $qb
     * @param bool         $active
     *
     * @return QueryBuilder
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
    public function findAllActiveByCollectivity(Collectivity $collectivity, bool $active = true, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        $this->addCollectivityClause($qb, $collectivity);
        $this->addActiveClause($qb, $active);
        $this->addOrder($qb, $order);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
