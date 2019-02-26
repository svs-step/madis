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

namespace App\Application\Doctrine\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Abstract Class CRUDRepository
 * The goal of this class if to pre-define Infrastructure of repositories.
 */
abstract class CRUDRepository implements CRUDRepositoryInterface
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
     * Get the model class name.
     *
     * @return string
     */
    abstract protected function getModelClass(): string;

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

    /**
     * Get all objects.
     *
     * @return mixed[]
     */
    public function findAll(array $order = []): array
    {
        $orderBy = [];
        foreach ($order as $key => $value) {
            $orderBy[$key] = $value;
        }

        return $this->registry
            ->getManager()
            ->getRepository($this->getModelClass())
            ->findBy([], $orderBy)
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
}
