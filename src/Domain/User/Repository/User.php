<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Domain\User\Model;

interface User extends CRUDRepositoryInterface
{
    /**
     * Get a user by it email.
     *
     * @param string $email The email to search
     *
     * @return Model\User|null The related user or null if not exists
     */
    public function findOneOrNullByEmail(string $email): ?Model\User;
}
