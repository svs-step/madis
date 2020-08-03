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
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Contractor extends CRUDRepository implements Repository\Contractor
{
    use RepositoryUtils;

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Contractor::class;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByCollectivity(Collectivity $collectivity, array $order = [])
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
        ;

        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

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
        $qb->andWhere($qb->expr()->eq('o.collectivity', ':collectivity'));
        $qb->setParameter('collectivity', $collectivity);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function count(array $criteria = [])
    {
        $qb = $this
            ->createQueryBuilder()
            ->select('count(o.id)')
        ;

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

//        $this->addTableOrder($qb, $orderColumn, $orderDir);

        $query = $qb->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($maxResults);

        return new Paginator($query);
    }

    // TODO Add table order
//    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
//    {
//        switch ($orderColumn) {
//            case 'nom':
//                break;
//            case 'collectivite':
//                break;
//            case 'clauses_contractuelles':
//                break;
//            case 'element_securite':
//                break;
//            case 'registre_traitements':
//                break;
//            case 'donnees_hors_eu':
//                break;
//        }
//    }

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
}
