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

namespace App\Infrastructure\ORM\User\Repository;

use App\Application\Doctrine\Repository\CRUDRepository;
use App\Domain\User\Model;
use App\Domain\User\Repository;

class User extends CRUDRepository implements Repository\User
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass(): string
    {
        return Model\User::class;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneOrNullByEmail(string $email): ?Model\User
    {
        return $this->createQueryBuilder()
            ->andWhere('o.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneOrNullByForgetPasswordToken(string $token): ?Model\User
    {
        return $this->createQueryBuilder()
            ->andWhere('o.forgetPasswordToken = :forgetPasswordToken')
            ->setParameter('forgetPasswordToken', $token)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
