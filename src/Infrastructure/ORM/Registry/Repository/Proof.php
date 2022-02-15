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
use App\Domain\Registry\Dictionary\ProofTypeDictionary;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class Proof implements Repository\Proof
{
    use RepositoryUtils;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * Proof constructor.
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
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder()
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
     * Find all data.
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
     * Get the model class name.
     */
    protected function getModelClass(): string
    {
        return Model\Proof::class;
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
     * Add order clause to query.
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
    public function findOneOrNullByTypeAndCollectivity(string $type, Collectivity $collectivity): ?Model\Proof
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq('o.type', ':type'));
        $qb->andWhere($qb->expr()->eq('o.collectivity', ':collectivity'));
        $qb->setParameters([
            'type'         => $type,
            'collectivity' => $collectivity,
        ]);
        $qb->addOrderBy('o.createdAt', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
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

    /**
     * {@inheritdoc}
     */
    public function averageProofFiled(array $collectivities = [])
    {
        $sql = 'SELECT AVG(a.rcount) FROM (
            SELECT COUNT(rp.id) as rcount
            FROM user_collectivity uc
            LEFT OUTER JOIN registry_proof rp ON uc.id = rp.collectivity_id
            WHERE uc.active = 1';

        if (!empty($collectivities)) {
            $sql .= ' AND uc.id IN (';
            $sql .= \implode(',', \array_map(function ($collectivity) {
                return '\'' . $collectivity->getId() . '\'';
            }, $collectivities));
            $sql .= ') ';
        }

        $sql .= ' 
            GROUP BY uc.id
        ) a';

        $stmt = $this->getManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * {@inheritdoc}
     */
    public function averageBalanceSheetProof(array $collectivities = [])
    {
        $sql = 'SELECT AVG(a.rcount) FROM (
            SELECT IF(COUNT(rp.id) > 0, 1, 0) as rcount
            FROM user_collectivity uc
            LEFT OUTER JOIN registry_proof rp ON (uc.id = rp.collectivity_id AND rp.created_at >= NOW() - INTERVAL 1 YEAR AND rp.type = "' . ProofTypeDictionary::TYPE_BALANCE_SHEET . '")
            WHERE uc.active = 1';

        if (!empty($collectivities)) {
            $sql .= ' AND uc.id IN (';
            $sql .= \implode(',', \array_map(function ($collectivity) {
                return '\'' . $collectivity->getId() . '\'';
            }, $collectivities));
            $sql .= ') ';
        }

        $sql .= ' 
            GROUP BY uc.id
        ) a';

        $stmt = $this->getManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchColumn();
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
            case 'nom':
                $queryBuilder->addOrderBy('o.name', $orderDir);
                break;
            case 'collectivite':
                $queryBuilder->addOrderBy('collectivite.name', $orderDir);
                break;
            case 'type':
                $queryBuilder->addSelect('(case
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_MESUREMENT . '\' THEN 1
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_CERTIFICATION . '\' THEN 2
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_OTHER . '\' THEN 3
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_BALANCE_SHEET . '\' THEN 4
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_IT_CHARTER . '\' THEN 5
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_CONTRACT . '\' THEN 6
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_DELIBERATION . '\' THEN 7
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_CONCERNED_PEOPLE_REQUEST . '\' THEN 8
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_POLICY_MANAGEMENT . '\' THEN 9
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_POLICY_PROTECTION . '\' THEN 10
                WHEN o.type = \'' . ProofTypeDictionary::TYPE_SENSITIZATION . '\' THEN 11
                ELSE 12 END) AS HIDDEN hidden_type')
                    ->addOrderBy('hidden_type', $orderDir);
                break;
            case 'commentaire':
                $queryBuilder->addOrderBy('o.comment', $orderDir);
                break;
            case 'date':
                $queryBuilder->addOrderBy('o.createdAt', $orderDir);
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
                    $queryBuilder->andWhere('collectivite.name LIKE :nom')
                        ->setParameter('nom', '%' . $search . '%');
                    break;
                case 'type':
                    $this->addWhereClause($queryBuilder, 'type', $search);
                    break;
                case 'commentaire':
                    $this->addWhereClause($queryBuilder, 'comment', '%' . $search . '%', 'LIKE');
                    break;
                case 'date':
                    $queryBuilder->andWhere('o.createdAt LIKE :date')
                        ->setParameter('date', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
            }
        }
    }
}
