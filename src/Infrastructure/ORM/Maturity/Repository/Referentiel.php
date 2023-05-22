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
use App\Application\Traits\RepositoryUtils;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Repository;
use App\Domain\User\Repository\Collectivity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Referentiel extends CRUDRepository implements Repository\Referentiel
{
    use RepositoryUtils;

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Referentiel::class;
    }

    public function findAllByCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();
        // TODO Gestion des droits

        $qb = $qb->getQuery();
        $qb->setFirstResult(0);
        $qb->setMaxResults(100);

        return new Paginator($qb);
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
            case 'name':
                $queryBuilder->addOrderBy('o.name', $orderDir);
                break;
            case 'description':
                $queryBuilder->addOrderBy('o.description', $orderDir);
                break;
            case 'updatedAt':
                $queryBuilder->addOrderBy('o.updatedAt', $orderDir);
                break;
        }
    }

    private function addTableSearches(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'name':
                    $this->addWhereClause($queryBuilder, 'name', '%' . $search . '%', 'LIKE');
                    break;
                case 'description':
                    $this->addWhereClause($queryBuilder, 'description', '%' . $search . '%', 'LIKE');
                    break;
                case 'updatedAt':
                    $queryBuilder->andWhere('o.updatedAt LIKE :date')
                        ->setParameter('date', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
            }
        }
    }
}
