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

namespace App\Domain\User\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

class UserRoleDictionary extends SimpleDictionary
{
    const ROLE_PREVIEW = 'ROLE_PREVIEW';
    const ROLE_USER    = 'ROLE_USER';
    const ROLE_ADMIN   = 'ROLE_ADMIN';

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
            self::ROLE_PREVIEW => 'Lecteur',
            self::ROLE_USER    => 'Gestionnaire',
            self::ROLE_ADMIN   => 'Administrateur',
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
