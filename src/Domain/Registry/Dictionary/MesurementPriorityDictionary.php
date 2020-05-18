<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

use App\Application\Dictionary\SimpleDictionary;

class MesurementPriorityDictionary extends SimpleDictionary
{
    const PRIORITY_LOW    = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH   = 'high';

    public function __construct()
    {
        parent::__construct('registry_mesurement_priority', self::getPriorities());
    }

    /**
     * @return array
     */
    public static function getPriorities()
    {
        return [
            self::PRIORITY_LOW    => '1 - Basse',
            self::PRIORITY_NORMAL => '2 - Normale',
            self::PRIORITY_HIGH   => '3 - Haute',
        ];
    }

    /**
     * @return array
     */
    public static function getPrioritiesKeys()
    {
        return \array_keys(self::getPriorities());
    }

    /**
     * @return array
     */
    public static function getWeightPriorities()
    {
        return [
            self::PRIORITY_LOW    => 1,
            self::PRIORITY_NORMAL => 2,
            self::PRIORITY_HIGH   => 3,
        ];
    }

    /**
     * @return array
     */
    public static function getPrioritiesColors()
    {
        return [
            self::PRIORITY_LOW    => 'F2D600',
            self::PRIORITY_NORMAL => 'FF9F1A',
            self::PRIORITY_HIGH   => 'EB5A46',
        ];
    }

    /**
     * @return array
     */
    public static function getPrioritiesNameWithoutNumber()
    {
        return [
            self::PRIORITY_LOW    => 'Basse',
            self::PRIORITY_NORMAL => 'Normale',
            self::PRIORITY_HIGH   => 'Haute',
        ];
    }
}
