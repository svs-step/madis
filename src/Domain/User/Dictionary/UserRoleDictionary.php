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

namespace App\Domain\User\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class UserRoleDictionary extends SimpleDictionary
{
    public const ROLE_PREVIEW  = 'ROLE_PREVIEW';
    public const ROLE_USER     = 'ROLE_USER';
    public const ROLE_ADMIN    = 'ROLE_ADMIN';
    public const ROLE_REFERENT = 'ROLE_REFERENT';
    // Role uniquement pour l'API
    public const ROLE_API = 'ROLE_API';

    public function __construct()
    {
        parent::__construct('user_user_role', self::getRoles());
    }

    /**
     * Get an array of Roles.
     *
     * @return array
     */
    public static function getRoles()
    {
        return [
            self::ROLE_PREVIEW  => 'Lecteur',
            self::ROLE_USER     => 'Gestionnaire',
            self::ROLE_ADMIN    => 'Administrateur',
            self::ROLE_REFERENT => 'Référent multi-collectivité',
            // Ne pas ajouter le ROLE_API
        ];
    }

    /**
     * Get an array of Roles.
     *
     * @return array
     */
    public static function getFullRoles()
    {
        return [
            self::ROLE_PREVIEW  => 'Lecteur',
            self::ROLE_USER     => 'Gestionnaire',
            self::ROLE_ADMIN    => 'Administrateur',
            self::ROLE_REFERENT => 'Référent multi-collectivité',
            self::ROLE_API      => 'API',
        ];
    }

    /**
     * Get keys of the Roles array.
     *
     * @return array
     */
    public static function getRolesKeys()
    {
        return \array_keys(self::getRoles());
    }
}
