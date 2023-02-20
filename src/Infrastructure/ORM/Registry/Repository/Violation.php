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

use App\Application\Traits\RepositoryUtils;
use App\Domain\Registry\Dictionary\ViolationCauseDictionary;
use App\Domain\Registry\Dictionary\ViolationGravityDictionary;
use App\Domain\Registry\Dictionary\ViolationNatureDictionary;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class Violation implements Repository\Violation
{
    use RepositoryUtils;
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * Violation constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Get the registry manager
     * Since we use Doctrine, we expect to get EntityManagerInterface.
     *
     * @throws \Exception
     */
    protected function getManager(): EntityManagerInterface
    {
        $manager = $this->registry->getManager();

        if (!$manager instanceof EntityManagerInterface) {
            throw new \Exception('Registry Manager must be an instance of EntityManagerInterface #PHPStan');
        }

        return $manager;
    }

    /**
     * Create the base of QueryBuilder to use for repository calls.
     *
     * @throws \Exception
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getManager()
            ->createQueryBuilder()
            ->select('o')
            ->from($this->getModelClass(), 'o')
        ;
    }

    /**
     * Insert an object.
     *
     * @param mixed $object
     *
     * @throws \Exception
     */
    public function insert($object): void
    {
        $this->getManager()->persist($object);
        $this->getManager()->flush();
    }

    /**
     * Update an object.
     *
     * @param mixed $object
     *
     * @throws \Exception
     */
    public function update($object): void
    {
        $this->getManager()->flush();
    }

    /**
     * Create an object.
     *
     * @return mixed
     */
    public function create()
    {
        $class = $this->getModelClass();

        return new $class();
    }

    /**
     * Remove an object.
     *
     * @param mixed $object
     *
     * @throws \Exception
     */
    public function remove($object): void
    {
        $this->getManager()->remove($object);
        $this->getManager()->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function findAll(bool $deleted = false): array
    {
        $qb = $this->createQueryBuilder();

        if ($deleted) {
            $qb->andWhere('o.deletedAt is not null');
        } else {
            $qb->andWhere('o.deletedAt is null');
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get an object by ID.
     *
     * @param string $id The ID to find
     *
     * @return object|null
     */
    public function findOneById(string $id)
    {
        return $this->registry
            ->getManager()
            ->getRepository($this->getModelClass())
            ->find($id)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\Violation::class;
    }

    /**
     * Add a where clause.
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
     * Add archive clause to query.
     */
    protected function addArchivedClause(QueryBuilder $qb, bool $archived = false): QueryBuilder
    {
        // Get not archived
        if (!$archived) {
            return $qb->andWhere('o.deletedAt is null');
        }

        // Get archived
        return $qb->andWhere('o.deletedAt is not null');
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
     * Add order to query.
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
     *
     * @throws \Exception
     */
    public function findAllByCollectivity(Collectivity $collectivity, bool $deleted = false, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        $this->addCollectivityClause($qb, $collectivity);

        if ($deleted) {
            $qb->andWhere('o.deletedAt is not null');
        } else {
            $qb->andWhere('o.deletedAt is null');
        }

        $this->addOrder($qb, $order);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
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
     *
     * @throws \Exception
     */
    public function findAllArchivedByCollectivity(Collectivity $collectivity, bool $archived = false, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        $this->addCollectivityClause($qb, $collectivity);
        $this->addArchivedClause($qb, $archived);
        $this->addOrder($qb, $order);

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
        $this->addCollectivityClause($qb, $collectivity);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOrNullLastUpdateByCollectivity(Collectivity $collectivity): ?Model\Violation
    {
        $qb = $this->createQueryBuilder();

        $this->addCollectivityClause($qb, $collectivity);
        $qb->addOrderBy('o.updatedAt', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function count(array $criteria = [])
    {
        $qb = $this
                ->createQueryBuilder()
                ->select('count(o.id)')
        ;

        if (\array_key_exists('archive', $criteria)) {
            $this->addArchivedClause($qb, $criteria['archive']);
            unset($criteria['archive']);
        }

        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
            $qb->leftJoin('o.collectivity', 'collectivite');
            $this->addInClauseCollectivities($qb, $criteria['collectivity']->toArray());
            unset($criteria['collectivity']);
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
        $qb = $this->createQueryBuilder();

        if (\array_key_exists('archive', $criteria)) {
            $this->addArchivedClause($qb, $criteria['archive']);
            unset($criteria['archive']);
        }

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
            case 'collectivite':
                $queryBuilder->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'date':
                $queryBuilder->addOrderBy('o.date', $orderDir);
                break;
            case 'nature':
                $queryBuilder->addSelect('(case
                WHEN o.violationNature = \'' . ViolationNatureDictionary::NATURE_INTEGRITY . '\' THEN 1
                WHEN o.violationNature = \'' . ViolationNatureDictionary::NATURE_CONFIDENTIALITY . '\' THEN 2
                WHEN o.violationNature = \'' . ViolationNatureDictionary::NATURE_AVAILABILITY . '\' THEN 3
                ELSE 4 END) AS HIDDEN hidden_violation_nature')
                    ->addOrderBy('hidden_violation_nature', $orderDir);
                break;
            case 'cause':
                $queryBuilder->addSelect('(case
                WHEN o.cause = \'' . ViolationCauseDictionary::CAUSE_EXTERNAL_ACCIDENTAL . '\' THEN 1
                WHEN o.cause = \'' . ViolationCauseDictionary::CAUSE_EXTERNAL_MALICIOUS . '\' THEN 2
                WHEN o.cause = \'' . ViolationCauseDictionary::CAUSE_INTERNAL_ACCIDENTAL . '\' THEN 3
                WHEN o.cause = \'' . ViolationCauseDictionary::CAUSE_INTERNAL_MALICIOUS . '\' THEN 4
                WHEN o.cause = \'' . ViolationCauseDictionary::CAUSE_UNKNOWN . '\' THEN 5
                ELSE 6 END) AS HIDDEN hidden_cause')
                    ->addOrderBy('hidden_cause', $orderDir);
                break;
            case 'gravity':
                $queryBuilder->addSelect('(case
                WHEN o.gravity = \'' . ViolationGravityDictionary::GRAVITY_IMPORTANT . '\' THEN 1
                WHEN o.gravity = \'' . ViolationGravityDictionary::GRAVITY_LIMITED . '\' THEN 2
                WHEN o.gravity = \'' . ViolationGravityDictionary::GRAVITY_MAXIMUM . '\' THEN 3
                WHEN o.gravity = \'' . ViolationGravityDictionary::GRAVITY_NEGLIGIBLE . '\' THEN 4
                ELSE 4 END) AS HIDDEN hidden_gravity')
                    ->addOrderBy('hidden_gravity', $orderDir);
                break;
            case 'createdAt':
                $queryBuilder->addOrderBy('o.createdAt', $orderDir);
                break;
            case 'updatedAt':
                $queryBuilder->addOrderBy('o.updatedAt', $orderDir);
                break;
        }
    }

    private function addTableWhere(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'collectivite':
                    $queryBuilder->andWhere('collectivite.name LIKE :nom')
                        ->setParameter('nom', '%' . $search . '%');
                    break;
                case 'date':
                    $queryBuilder->andWhere('o.date LIKE :date')
                        ->setParameter('date', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
                case 'nature':
                    $this->addWhereClause($queryBuilder, 'violationNature', $search);
                    break;
                case 'inProgress':
                    $this->addWhereClause($queryBuilder, 'in_progress', $search);
                    break;
                case 'cause':
                    $this->addWhereClause($queryBuilder, 'cause', $search);
                    break;
                case 'gravity':
                    $this->addWhereClause($queryBuilder, 'gravity', $search);
                    break;
                case 'notification':
                    $this->addWhereClause($queryBuilder, 'notification', $search);
                    break;
                case 'createdAt':
                    $queryBuilder->andWhere('o.createdAt LIKE :date')
                        ->setParameter('date', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
                case 'updatedAt':
                    $queryBuilder->andWhere('o.updatedAt LIKE :date')
                        ->setParameter('date', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
        }
        }
    }
}
