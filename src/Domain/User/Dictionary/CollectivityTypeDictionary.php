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

class CollectivityTypeDictionary extends SimpleDictionary
{
    const TYPE_COMMUNE            = 'commune';
    const TYPE_CCAS               = 'ccas';
    const TYPE_EPCI               = 'epci';
    const TYPE_CIAS               = 'cias';
    const TYPE_DEPARTMENTAL_UNION = 'departmental_union';
    const TYPE_OTHER              = 'other';

    public function __construct()
    {
        parent::__construct('user_collectivity_type', self::getTypes());
    }

    /**
     * Get an array of Roles.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_COMMUNE            => 'Commune',
            self::TYPE_CCAS               => 'CCAS',
            self::TYPE_EPCI               => 'EPCI',
            self::TYPE_CIAS               => 'CIAS',
            self::TYPE_DEPARTMENTAL_UNION => 'Syndicat départemental',
            self::TYPE_OTHER              => 'Autre',
        ];
    }

    /**
     * Get keys of the Roles array.
     *
     * @return array
     */
    public static function getTypesKeys()
    {
        return \array_keys(self::getTypes());
    }
}
