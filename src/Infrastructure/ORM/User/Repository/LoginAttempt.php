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

namespace App\Infrastructure\ORM\User\Repository;

use App\Domain\User\Model;
use App\Domain\User\Repository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class LoginAttempt implements Repository\LoginAttempt
{
    protected ManagerRegistry $registry;

    protected EntityManagerInterface $manager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    protected function getModelClass(): string
    {
        return Model\LoginAttempt::class;
    }

    /**
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
     * @throws \Exception
     *
     * @return QueryBuilder
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
     * @param Model\User $object
     *
     * @throws \Exception
     */
    public function insert($object): void
    {
        $this->getManager()->persist($object);
        $this->getManager()->flush();
    }

    /**
     * @param Model\User $object
     *
     * @throws \Exception
     */
    public function update($object): void
    {
        $v = $object;
        $this->getManager()->flush();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneOrNullByIpAndEmail(string $ip, string $email): ?Model\LoginAttempt
    {
        return $this->createQueryBuilder()
            ->andWhere('o.ip = :ip')
            ->andWhere('o.email = :email')
            ->setParameter('ip', $ip)
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
