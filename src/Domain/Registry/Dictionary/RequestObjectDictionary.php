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

namespace App\Domain\Registry\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

class RequestObjectDictionary extends SimpleDictionary
{
    const OBJECT_CORRECT          = 'correct';
    const OBJECT_DELETE           = 'delete';
    const OBJECT_WITHDRAW_CONSENT = 'withdraw_consent';
    const OBJECT_ACCESS           = 'access';
    const OBJECT_DATA_PORTABILITY = 'data_portability';
    const OBJECT_LIMIT_TREATMENT  = 'limit_treatment';
    const OBJECT_OTHER            = 'other';

    public function __construct()
    {
        parent::__construct('registry_request_object', self::getObjects());
    }

    /**
     * Get an array of Objects.
     *
     * @return array
     */
    public static function getObjects()
    {
        return [
            self::OBJECT_CORRECT          => 'Rectifier des données',
            self::OBJECT_DELETE           => 'Supprimer des données',
            self::OBJECT_WITHDRAW_CONSENT => 'Retirer le consentement',
            self::OBJECT_ACCESS           => 'Accéder à des données',
            self::OBJECT_DATA_PORTABILITY => 'Portabilité des données',
            self::OBJECT_LIMIT_TREATMENT  => 'Limiter le traitement',
            self::OBJECT_OTHER            => 'Autre',
        ];
    }

    /**
     * Get keys of the Objects array.
     *
     * @return array
     */
    public static function getObjectsKeys()
    {
        return \array_keys(self::getObjects());
    }
}
