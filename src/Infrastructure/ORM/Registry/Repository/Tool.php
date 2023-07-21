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
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class Tool extends CRUDRepository implements Repository\Tool
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

    protected function getModelClass(): string
    {
        return Model\Tool::class;
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
        $query = $this->createQueryBuilder()
            ->leftJoin('o.collectivity', 'collectivity')
            ->leftJoin('o.contractors', 'contractors')
        ;
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
                case 'name':
                    $queryBuilder->andWhere('o.name LIKE :name')
                        ->setParameter('name', '%' . $search . '%');
                    break;
                case 'editor':
                    $queryBuilder->andWhere('o.editor LIKE :editor')
                        ->setParameter('editor', '%' . $search . '%');
                    break;
                case 'access_control':
                    $queryBuilder->andWhere('o.access_control.check = :access_control')
                        ->setParameter('access_control', $search);
                    break;
                case 'deletion':
                    $queryBuilder->andWhere('o.deletion.check = :deletion')
                        ->setParameter('deletion', $search);
                    break;
                case 'tracking':
                    $queryBuilder->andWhere('o.tracking.check = :tracking')
                        ->setParameter('tracking', $search);
                    break;
                case 'other':
                    $queryBuilder->andWhere('o.other.check = :other')
                        ->setParameter('other', $search);
                    break;
                case 'type':
                    $this->addWhereClause($queryBuilder, $columnName, '%' . $search . '%', 'LIKE');
                    break;
                case 'collectivity':
                    $queryBuilder->andWhere('collectivity.name LIKE :colnom')
                        ->setParameter('colnom', '%' . $search . '%');
                    break;
                case 'contractors':
                    $queryBuilder->andWhere('contractors.name LIKE :st_nom')
                        ->setParameter('st_nom', '%' . $search . '%');
                    break;
                case 'createdAt':
                    if (is_string($search)) {
                        $queryBuilder->andWhere('o.createdAt BETWEEN :created_start_date AND :created_finish_date')
                            ->setParameter('created_start_date', date_create_from_format('d/m/y', substr($search, 0, 8))->format('Y-m-d 00:00:00'))
                            ->setParameter('created_finish_date', date_create_from_format('d/m/y', substr($search, 11, 8))->format('Y-m-d 23:59:59'));
                    }
                    break;
                case 'updatedAt':
                    if (is_string($search)) {
                        $queryBuilder->andWhere('o.updatedAt BETWEEN :updated_start_date AND :updated_finish_date')
                            ->setParameter('updated_start_date', date_create_from_format('d/m/y', substr($search, 0, 8))->format('Y-m-d 00:00:00'))
                            ->setParameter('updated_finish_date', date_create_from_format('d/m/y', substr($search, 11, 8))->format('Y-m-d 23:59:59'));
                    }
                    break;
                default:
                    $queryBuilder->andWhere('o.' . $columnName . '.check = :search' . $columnName)
                    ->setParameter(':search' . $columnName, $search);
                    break;
            }
        }
    }

    private function addTableOrder(QueryBuilder $queryBuilder, $orderColumn, $orderDir)
    {
        switch ($orderColumn) {
            case 'collectivity':
                $queryBuilder->addOrderBy('collectivity.name', $orderDir);
                break;
            case 'type':
            case 'editor':
            case 'createdAt':
            case 'updatedAt':
            case 'name':
                $queryBuilder->addOrderBy('o.' . $orderColumn, $orderDir);
                break;
            default:
                // $queryBuilder->leftJoin(Model\Embeddable\ComplexChoice::class, 'cc', 'WITH', 'cc')
                $queryBuilder->addOrderBy('o.' . $orderColumn . '.check', $orderDir);
                break;
        }
    }

    public function findAllByCollectivity(Collectivity $collectivity = null, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        if (!\is_null($collectivity)) {
            $this->addCollectivityClause($qb, $collectivity);
        }
        $this->addOrder($qb, $order);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
