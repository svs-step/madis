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

class UserMoreInfoDictionary extends SimpleDictionary
{
    public const MOREINFO_TREATMENT    = 'MOREINFO_TREATMENT';
    public const MOREINFO_INFORMATIC   = 'MOREINFO_INFORMATIC';
    public const MOREINFO_OPERATIONNAL = 'MOREINFO_OPERATIONNAL';
    public const MOREINFO_DPD          = 'MOREINFO_DPD';

    public function __construct()
    {
        parent::__construct('user_user_moreInfo', self::getMoreInfos());
    }

    /**
     * Get an array of MoreInfos.
     *
     * @return array
     */
    public static function getMoreInfos()
    {
        return [
            self::MOREINFO_TREATMENT    => 'Responsable de traitement',
            self::MOREINFO_OPERATIONNAL => 'Référent RGPD',
            self::MOREINFO_INFORMATIC   => 'Responsable informatique',
            self::MOREINFO_DPD          => 'Délégué à la protection des données',
        ];
    }

    /**
     * Get keys of the MoreInfos array.
     *
     * @return array
     */
    public static function getMoreInfosKeys()
    {
        return \array_keys(self::getMoreInfos());
    }
}
