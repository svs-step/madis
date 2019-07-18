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

    /**
     * CRUDRepository constructor.
     *
     * @param RegistryInterface $registry
     */
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
     * @param array $order
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
