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
use App\Domain\Registry\Dictionary\RequestObjectDictionary;
use App\Domain\Registry\Dictionary\RequestStateDictionary;
use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class Request implements Repository\Request
{
    use RepositoryUtils;

    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * Request constructor.
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function findOneById(string $id)
    {
        return $this->registry
            ->getManager()
            ->getRepository($this->getModelClass())
            ->find($id)
            ;
    }

    protected function getModelClass(): string
    {
        return Model\Request::class;
    }

    /**
     * Add archived clause in query.
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
    public function findAllArchived(bool $archived = false, array $order = [])
    {
        $qb = $this->createQueryBuilder();

        $this->addArchivedClause($qb, $archived);
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
    public function findOneOrNullLastUpdateByCollectivity(Collectivity $collectivity): ?Model\Request
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
        $qb = $this->createQueryBuilder()
            ->addSelect('collectivite')
            ->leftJoin('o.collectivity', 'collectivite');

        if (\array_key_exists('archive', $criteria)) {
            $this->addArchivedClause($qb, $criteria['archive']);
            unset($criteria['archive']);
        }

        if (isset($criteria['collectivity']) && $criteria['collectivity'] instanceof Collection) {
            $this->addInClauseCollectivities($qb, $criteria['collectivity']->toArray());
            unset($criteria['collectivity']);
        }

        $this->addTableOrder($qb, $orderColumn, $orderDir);
        $this->addTableWhere($qb, $searches);

        foreach ($criteria as $key => $value) {
            $this->addWhereClause($qb, $key, $value);
        }

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
            case 'personne_concernee':
                $queryBuilder->addSelect('IFELSE (o.applicant.concernedPeople = 1, 
                    CONCAT(o.applicant.firstName, \' \', o.applicant.lastName), 
                    CONCAT(o.concernedPeople.firstName, \' \', o.applicant.lastName))
                    AS HIDDEN person_name')
                ->addOrderBy('person_name', $orderDir);
                break;
            case 'date_demande':
                $queryBuilder->addOrderBy('o.date', $orderDir);
                break;
            case 'objet_demande':
                $queryBuilder->addSelect('(case
                WHEN o.object = \'' . RequestObjectDictionary::OBJECT_ACCESS . '\' THEN 1
                WHEN o.object = \'' . RequestObjectDictionary::OBJECT_OTHER . '\' THEN 2
                WHEN o.object = \'' . RequestObjectDictionary::OBJECT_LIMIT_TREATMENT . '\' THEN 3
                WHEN o.object = \'' . RequestObjectDictionary::OBJECT_DATA_PORTABILITY . '\' THEN 4
                WHEN o.object = \'' . RequestObjectDictionary::OBJECT_CORRECT . '\' THEN 5
                WHEN o.object = \'' . RequestObjectDictionary::OBJECT_WITHDRAW_CONSENT . '\' THEN 6
                WHEN o.object = \'' . RequestObjectDictionary::OBJECT_DELETE . '\' THEN 7
                ELSE 8 END) AS HIDDEN hidden_object')
                    ->addOrderBy('hidden_object', $orderDir);
                break;
            case 'demande_complete':
                $queryBuilder->addOrderBy('o.complete', $orderDir);
                break;
            case 'demandeur_legitime':
                $queryBuilder->addOrderBy('o.legitimateApplicant', $orderDir);
                break;
            case 'demande_legitime':
                $queryBuilder->addOrderBy('o.legitimateRequest', $orderDir);
                break;
            case 'etat_demande':
                $queryBuilder->addSelect('(case
                WHEN o.state = \'' . RequestStateDictionary::STATE_TO_TREAT . '\' THEN 1
                WHEN o.state = \'' . RequestStateDictionary::STATE_DENIED . '\' THEN 2
                WHEN o.state = \'' . RequestStateDictionary::STATE_COMPLETED_CLOSED . '\' THEN 3
                WHEN o.state = \'' . RequestStateDictionary::STATE_AWAITING_CONFIRMATION . '\' THEN 4
                WHEN o.state = \'' . RequestStateDictionary::STATE_ON_REQUEST . '\' THEN 5
                WHEN o.state = \'' . RequestStateDictionary::STATE_AWAITING_SERVICE . '\' THEN 6
                ELSE 7 END) AS HIDDEN hidden_state')
                    ->addOrderBy('hidden_state', $orderDir);
                break;
        }
    }

    private function addTableWhere(QueryBuilder $queryBuilder, $searches)
    {
        foreach ($searches as $columnName => $search) {
            switch ($columnName) {
                case 'collectivite':
                    $queryBuilder->andWhere('collectivite.name LIKE :collectivite_nom')
                        ->setParameter('collectivite_nom', '%' . $search . '%');
                    break;
                case 'personne_concernee':
                    $queryBuilder->andWhere('IFELSE (o.applicant.concernedPeople = 1, 
                        CONCAT(o.applicant.firstName, \' \', o.applicant.lastName), 
                        CONCAT(o.concernedPeople.firstName, \' \', o.applicant.lastName))
                        LIKE :person_name')
                        ->setParameter('person_name', '%' . $search . '%');
                    break;
                case 'date_demande':
                    $queryBuilder->andWhere('o.date LIKE :date')
                        ->setParameter('date', date_create_from_format('d/m/Y', $search)->format('Y-m-d') . '%');
                    break;
                case 'objet_demande':
                    $this->addWhereClause($queryBuilder, 'object', $search);
                    break;
                case 'demande_complete':
                    $this->addWhereClause($queryBuilder, 'complete', $search);
                    break;
                case 'demandeur_legitime':
                    $this->addWhereClause($queryBuilder, 'legitimateApplicant', $search);
                    break;
                case 'demande_legitime':
                    $this->addWhereClause($queryBuilder, 'legitimateRequest', $search);
                    break;
                case 'etat_demande':
                    $this->addWhereClause($queryBuilder, 'state', $search);
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAllLate(): array
    {
        $now       = new \DateTime();
        $lastMonth = $now->sub(\DateInterval::createFromDateString('1 month'));

        return $this->createQueryBuilder()
            ->andWhere('o.updatedAt < :lastmonth')
            ->setParameter('lastmonth', $lastMonth->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }
}
