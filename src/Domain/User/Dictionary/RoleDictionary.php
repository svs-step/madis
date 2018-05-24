<?php

declare(strict_types=1);

namespace App\Domain\User\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

class RoleDictionary extends SimpleDictionary
{
    const ROLE_PREVIEW = 'ROLE_PREVIEW';
    const ROLE_USER    = 'ROLE_USER';
    const ROLE_ADMIN   = 'ROLE_ADMIN';

    public function __construct()
    {
        parent::__construct('user_role', self::getRoles());
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
