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

namespace App\Domain\User\Repository;

use App\Application\DDD\Repository\CRUDRepositoryInterface;
use App\Application\Doctrine\Repository\DataTablesRepository;
use App\Domain\User\Model;

interface User extends CRUDRepositoryInterface, DataTablesRepository
{
    /**
     * Get a user by it email.
     *
     * @param string $email The email to search
     *
     * @return Model\User|null The related user or null if not exists
     */
    public function findOneOrNullByEmail(string $email): ?Model\User;

    /**
     * Get a user by it forgetPasswordToken.
     *
     * @param string $token The token to search
     *
     * @return Model\User|null The related user or null if not exists
     */
    public function findOneOrNullByForgetPasswordToken(string $token): ?Model\User;

    /**
     * Get the last login user by collectivity.
     *
     * @return Model\User|null The related user or null if not exists
     */
    public function findOneOrNullLastLoginUserByCollectivity(Model\Collectivity $collectivity): ?Model\User;

    /**
     * Get a user by it ssoKey.
     *
     * @param string $ssoKey The ssokey to search
     *
     * @return Model\User|null The related user or null if not exists
     */
    public function findOneOrNullBySsoKey(string $ssoKey): ?Model\User;

    /**
     * get the users that have never logged in and exist for more than 6 months.
     */
    public function findAllNoLogin();

    /**
     * get the users that have have specified roles.
     */
    public function findNonDpoUsers();

    public function findNonDpoUsersForCollectivity(Model\Collectivity $collectivity);
}
