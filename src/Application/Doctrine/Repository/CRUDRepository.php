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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Abstract Class CRUDRepository
 * The goal of this class if to pre-define Infrastructure of repositories.
 */
abstract class CRUDRepository implements CRUDRepositoryInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * CRUDRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Get the model class name.
     */
    abstract protected function getModelClass(): string;

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
            throw new \Exception('Registry Manager must be an instance of EntityManagerInterface');
        }

        return $manager;
    }

    /**
     * Create the base of QueryBuilder to use for repository calls.
     *
     * @throws \Exception
     */
    protected function createQueryBuilder(): \Doctrine\ORM\QueryBuilder
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
     * @throws \Exception
     */
    public function update($object): void
    {
        $this->getManager()->flush();
    }

    /**
     * Create an object.
     */
    public function create()
    {
        $class = $this->getModelClass();

        return new $class();
    }

    /**
     * Remove an object.
     *
     * @throws \Exception
     */
    public function remove($object): void
    {
        $this->getManager()->remove($object);
        $this->getManager()->flush();
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

    public function findBy(array $criterias)
    {
        return $this->registry
            ->getManager()
            ->getRepository($this->getModelClass())
            ->findBy($criterias)
        ;
    }
}
