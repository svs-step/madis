<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
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
    protected function getModelClass(): string
    {
        return Model\Treatment::class;
    }

    protected function addCollectivityClause(QueryBuilder $qb, Collectivity $collectivity): QueryBuilder
    {
        return $qb
            ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
            ;
    }

    protected function addActiveClause(QueryBuilder $qb, bool $active = true): QueryBuilder
    {
        return $qb
            ->andWhere('o.active = :active')
            ->setParameter('active', $active)
            ;
    }

    protected function addOrder(QueryBuilder $qb, array $order = []): QueryBuilder
    {
        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        return $qb;
    }

    /**
     * Find all treatments by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param array        $order        Order the data
     *
     * @return array The array of treatments given by the collectivity
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
     * Find all active treatments by associated collectivity.
     *
     * @param Collectivity $collectivity The collectivity to search with
     * @param bool         $active       Get all active / inactive treatments
     * @param array        $order        Order the data
     *
     * @return array The array of treatments given by the collectivity
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
