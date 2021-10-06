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
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\Collection;
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
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');

        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
            $qb->leftJoin('o.collectivity', 'collectivite');
            $this->addInClauseCollectivities($qb, $criteria['collectivity']->toArray());
            unset($criteria['collectivity']);
        }

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findPaginated($firstResult, $maxResults, $orderColumn, $orderDir, $searches, $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('o.collectivity', 'collectivite')
            ->addSelect('collectivite');

        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
            $this->addInClauseCollectivities($qb, $criteria['collectivity']->toArray());
            unset($criteria['collectivity']);
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
            case 'collectivite':
                $queryBuilder->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'clauses_contractuelles':
                $queryBuilder->addOrderBy('o.contractualClausesVerified', $orderDir);
                break;
            case 'element_securite':
                $queryBuilder->addOrderBy('o.adoptedSecurityFeatures', $orderDir);
                break;
            case 'registre_traitements':
                $queryBuilder->addOrderBy('o.maintainsTreatmentRegister', $orderDir);
                break;
            case 'donnees_hors_eu':
                $queryBuilder->addOrderBy('o.sendingDataOutsideEu', $orderDir);
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
                case 'collectivite':
                    $queryBuilder->andWhere('collectivite.name LIKE :collectivite_name')
                        ->setParameter('collectivite_name', '%' . $search . '%');
                    break;
                case 'clauses_contractuelles':
                    $this->addWhereClause($queryBuilder, 'contractualClausesVerified', '%' . $search . '%', 'LIKE');
                    break;
                case 'element_securite':
                    $this->addWhereClause($queryBuilder, 'adoptedSecurityFeatures', $search);
                    break;
                case 'registre_traitements':
                    $this->addWhereClause($queryBuilder, 'maintainsTreatmentRegister', $search);
                    break;
                case 'donnees_hors_eu':
                    $this->addWhereClause($queryBuilder, 'sendingDataOutsideEu', $search);
                    break;
            }
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
