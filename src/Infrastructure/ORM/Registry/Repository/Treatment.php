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
use App\Domain\Registry\Dictionary\TreatmentStatutDictionary;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Treatment extends CRUDRepository implements Repository\Treatment
{
    use RepositoryUtils;

    protected function getModelClass(): string
    {
        return Model\Treatment::class;
    }

    /**
     * Add collectivity clause to query.
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
     */
    protected function addOrder(QueryBuilder $qb, array $order = []): QueryBuilder
    {
        foreach ($order as $key => $dir) {
            $qb->addOrderBy("o.{$key}", $dir);
        }

        return $qb;
    }

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

    public function findAllActiveByCollectivity(?Collectivity $collectivity = null, bool $active = true, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        if (!\is_null($collectivity)) {
            $this->addCollectivityClause($qb, $collectivity);
        }
        $this->addActiveClause($qb, $active);
        $this->addOrder($qb, $order);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function countAllByCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');
        $this->addCollectivityClause($qb, $collectivity);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findOneOrNullLastUpdateByCollectivity(Collectivity $collectivity): ?Model\Treatment
    {
        $qb = $this->createQueryBuilder();

        $this->addCollectivityClause($qb, $collectivity);
        $qb->addOrderBy('o.updatedAt', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function countAllActiveByCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');
        $this->addCollectivityClause($qb, $collectivity);
        $this->addActiveClause($qb, true);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findAllByActiveCollectivity(bool $active = true, ?User $user = null)
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

    public function findAllActiveByCollectivityWithHasModuleConformiteTraitement($collectivity = null, bool $active = true, array $order = ['name' => 'ASC'])
    {
        $qb = $this->createQueryBuilder();

        if (!\is_null($collectivity)) {
            if (\is_array($collectivity)) {
                $qb
                    ->andWhere(
                        $qb->expr()->in('o.collectivity', ':collectivities')
                    )
                    ->setParameter('collectivities', $collectivity)
                ;
            } else {
                $this->addCollectivityClause($qb, $collectivity);
            }
        }
        $this->addActiveClause($qb, $active);
        $this->addOrder($qb, $order);

        $qb->leftJoin('o.collectivity', 'c')
            ->andWhere($qb->expr()->eq('c.hasModuleConformiteTraitement', ':active'))
            ->setParameter('active', true)
        ;

        $qb->leftJoin('o.conformiteTraitement', 'k')
            ->addSelect('k')
        ;

        $qb->leftJoin('k.analyseImpacts', 'a')
            // , 'WITH', 'a.dateValidation = (SELECT MAX(a2.dateValidation) FROM App\Domain\AIPD\Model\AnalyseImpact as a2 WHERE a2.conformiteTraitement = k)
            ->addSelect('a')
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function countAllWithNoConformiteTraitementByCollectivity(?Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('COUNT(o.id)');
        $this->addCollectivityClause($qb, $collectivity);
        $this->addActiveClause($qb, true);
        $qb->leftJoin('o.conformiteTraitement', 'cT')
            ->andWhere($qb->expr()->isNull('cT.id'))
        ;

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
        $qb = $this->createQueryBuilder()
            ->addSelect('collectivite')
            ->leftJoin('o.collectivity', 'collectivite')
            ->leftJoin('o.contractors', 'sous_traitants')
            ->leftJoin('o.dataCategories', 'data_categories')
        ;

        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
            $this->addInClauseCollectivities($qb, $criteria['collectivity']->toArray());
            unset($criteria['collectivity']);
        }

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

        $this->addTableOrder($qb, $orderColumn, $orderDir);
        $this->addTableSearches($qb, $searches);

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
            case 'collectivite':
                $queryBuilder->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'baseLegal':
                $queryBuilder->addOrderBy('o.legalBasis', $orderDir);
                break;
            case 'logiciel':
                // TODO order on joined table if tools module is active ?
                $queryBuilder->addOrderBy('o.software', $orderDir);
                break;
            case 'enTantQue':
                $queryBuilder->addOrderBy('o.author', $orderDir);
                break;
            case 'gestionnaire':
                $queryBuilder->addOrderBy('o.manager', $orderDir);
                break;
            case 'controleAcces':
                $queryBuilder->addOrderBy('o.securityAccessControl.check', $orderDir);
                break;
            case 'tracabilite':
                $queryBuilder->addOrderBy('o.securityTracability.check', $orderDir);
                break;
            case 'saving':
                $queryBuilder->addOrderBy('o.securitySaving.check', $orderDir);
                break;
            case 'update':
                $queryBuilder->addOrderBy('o.securityUpdate.check', $orderDir);
                break;
            case 'other':
                $queryBuilder->addOrderBy('o.securityOther.check', $orderDir);
                break;
            case 'entitledPersons':
                $queryBuilder->addOrderBy('o.securityEntitledPersons', $orderDir);
                break;
            case 'openAccounts':
                $queryBuilder->addOrderBy('o.securityOpenAccounts', $orderDir);
                break;
            case 'specificitiesDelivered':
                $queryBuilder->addOrderBy('o.securitySpecificitiesDelivered', $orderDir);
                break;
            case 'responsableTraitement':
                $queryBuilder->addOrderBy('o.coordonneesResponsableTraitement', $orderDir);
                break;
            case 'createdAt':
                $queryBuilder->addOrderBy('o.createdAt', $orderDir);
                break;
            case 'updatedAt':
                $queryBuilder->addOrderBy('o.updatedAt', $orderDir);
                break;
            case 'statut':
                $queryBuilder->addSelect('(case
                WHEN o.statut = \'' . TreatmentStatutDictionary::DRAFT . '\' THEN 1
                WHEN o.statut = \'' . TreatmentStatutDictionary::CHECKED . '\' THEN 2
                WHEN o.statut = \'' . TreatmentStatutDictionary::FINISHED . '\' THEN 3
                ELSE 4 END) AS HIDDEN hidden_statut')
                    ->addOrderBy('hidden_statut', $orderDir);
                break;
            case 'sensitiveData':
                $queryBuilder->leftJoin('o.dataCategories', 'dco', 'WITH', 'dco.sensible = 1')
                    ->addSelect('COUNT(dco.code) as sensitiveCount')
                    ->addOrderBy('sensitiveCount', $orderDir)
                    ->groupBy('o.id')
                ;
                break;
        }
    }

    private function addTableSearches(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'nom':
                    $this->addWhereClause($queryBuilder, 'name', '%' . $search . '%', 'LIKE');
                    break;
                case 'collectivite':
                    $queryBuilder->andWhere('collectivite.name LIKE :nom')
                        ->setParameter('nom', '%' . $search . '%');
                    break;
                case 'baseLegal':
                    $this->addWhereClause($queryBuilder, 'legalBasis', '%' . $search . '%', 'LIKE');
                    break;
                case 'logiciel':
                    // If collectivity has tools modules active, search in tools
                    $queryBuilder->leftJoin('o.tools', 'tools')
                        ->addSelect('GROUP_CONCAT(tools.name) as HIDDEN toolsNames')
                        ->groupBy('o.id')
                    ;

                    $queryBuilder->andHaving($queryBuilder->expr()->orX('(toolsNames LIKE :software_tool AND collectivite.hasModuleTools = 1)', '(o.software LIKE :software_tool AND collectivite.hasModuleTools = 0)'))
                        ->setParameter('software_tool', '%' . $search . '%')
                    ;

                    break;
                case 'enTantQue':
                    $this->addWhereClause($queryBuilder, 'author', '%' . $search . '%', 'LIKE');
                    break;
                case 'gestionnaire':
                    $this->addWhereClause($queryBuilder, 'manager', '%' . $search . '%', 'LIKE');
                    break;
                case 'sousTraitant':
                    $queryBuilder->andWhere('sous_traitants.name LIKE :st_nom')
                        ->setParameter('st_nom', '%' . $search . '%');
                    break;
                case 'controleAcces':
                    $queryBuilder->andWhere('o.securityAccessControl.check = :access_control')
                        ->setParameter('access_control', $search);
                    break;
                case 'tracabilite':
                    $queryBuilder->andWhere('o.securityTracability.check = :tracabilite')
                        ->setParameter('tracabilite', $search);
                    break;
                case 'saving':
                    $queryBuilder->andWhere('o.securitySaving.check = :saving')
                        ->setParameter('saving', $search);
                    break;
                case 'update':
                    $queryBuilder->andWhere('o.securityUpdate.check = :update')
                        ->setParameter('update', $search);
                    break;
                case 'other':
                    $queryBuilder->andWhere('o.securityOther.check = :other')
                        ->setParameter('other', $search);
                    break;
                case 'entitledPersons':
                    $queryBuilder->andWhere('o.securityEntitledPersons = :entitledPersons')
                        ->setParameter('entitledPersons', $search);
                    break;
                case 'openAccounts':
                    $queryBuilder->andWhere('o.securityOpenAccounts = :openAccounts')
                        ->setParameter('openAccounts', $search);
                    break;
                case 'specificitiesDelivered':
                    $this->addWhereClause($queryBuilder, 'securitySpecificitiesDelivered', '%' . $search . '%', 'LIKE');
                    // $queryBuilder->andWhere('o.securitySpecificitiesDelivered LIKE :specificitiesDelivered')
                    //    ->setParameter('specificitiesDelivered', '%' . $search . '%');
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
                case 'public':
                    $queryBuilder->andWhere('o.public = :public')
                        ->setParameter('public', $search);
                    break;
                case 'responsableTraitement':
                    $this->addWhereClause($queryBuilder, 'coordonneesResponsableTraitement', '%' . $search . '%', 'LIKE');
                    break;
                case 'sensitiveData':
                    if ($search) {
                        $queryBuilder
                            ->leftJoin('o.dataCategories', 'dcs', 'WITH', 'dcs.sensible = :sensitiveDatas')
                            ->addSelect('dcs.sensible AS sensitiveData')
                            ->andHaving('COUNT(dcs.code) > 0')
                            ->setParameter('sensitiveDatas', 1)
                            ->groupBy('o.id')
                        ;
                    } else {
                        $queryBuilder
                            ->leftJoin('o.dataCategories', 'dcs', 'WITH', 'dcs.sensible = :sensitiveDatas')
                            ->andHaving('COUNT(dcs.code) = 0')
                            ->setParameter('sensitiveDatas', 1)
                            ->groupBy('o.id')
                        ;
                    }

                    break;
                case 'statut':
                    $queryBuilder->andWhere('o.statut = :statut')
                        ->setParameter('statut', $search);
                    break;
            }
        }
    }

    public function resetClonedFromCollectivity(Collectivity $collectivity)
    {
        $qb = $this->createQueryBuilder();

        $qb->leftJoin('o.clonedFrom', 'c')
            ->andWhere('c.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity);

        $qb->update(['o.clonedFrom' => null]);
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
