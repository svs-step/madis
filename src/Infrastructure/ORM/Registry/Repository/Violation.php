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

use App\Domain\Registry\Model;
use App\Domain\Registry\Repository;
use App\Domain\User\Model\Collectivity;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

class Violation implements Repository\Violation
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Create the base of QueryBuilder to use for repository calls.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return $this->registry
            ->getManager()
            ->createQueryBuilder()
            ->select('o')
            ->from($this->getModelClass(), 'o')
            ;
    }

    /**
     * Insert an object.
     *
     * @param mixed $object
     */
    public function insert($object): void
    {
        $this->registry->getManager()->persist($object);
        $this->registry->getManager()->flush($object);
    }

    /**
     * Update an object.
     *
     * @param mixed $object
     */
    public function update($object): void
    {
        $this->registry->getManager()->flush($object);
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
     */
    public function remove($object): void
    {
        $this->registry->getManager()->remove($object);
        $this->registry->getManager()->flush($object);
    }

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
     * @return null|object
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
        return Model\Violation::class;
    }

    protected function addWhereClause(QueryBuilder $qb, string $key, $value): QueryBuilder
    {
        return $qb
            ->andWhere("o.{$key} = :{$key}_value")
            ->setParameter("{$key}_value", $value)
            ;
    }

    protected function addArchivedClause(QueryBuilder $qb, bool $archived = false): QueryBuilder
    {
        // Get not archived
        if (!$archived) {
            return $qb->andWhere('o.deletedAt is null');
        }

        // Get archived
        return $qb->andWhere('o.deletedAt is not null');
    }

    protected function addCollectivityClause(QueryBuilder $qb, Collectivity $collectivity): QueryBuilder
    {
        return $qb
            ->andWhere('o.collectivity = :collectivity')
            ->setParameter('collectivity', $collectivity)
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
     * {@inheritdoc}
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
}
