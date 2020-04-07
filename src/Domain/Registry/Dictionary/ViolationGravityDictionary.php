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

use App\Application\Dictionary\SimpleDictionary;

class ViolationGravityDictionary extends SimpleDictionary
{
    const GRAVITY_NEGLIGIBLE = 'negligible';
    const GRAVITY_LIMITED    = 'limited';
    const GRAVITY_IMPORTANT  = 'important';
    const GRAVITY_MAXIMUM    = 'maximum';

    public function __construct()
    {
        parent::__construct('registry_violation_gravity', self::getGravities());
    }

    /**
     * Get an array of Gravities.
     *
     * @return array
     */
    public static function getGravities()
    {
        return [
            self::GRAVITY_NEGLIGIBLE => 'Négligeable',
            self::GRAVITY_LIMITED    => 'Limité',
            self::GRAVITY_IMPORTANT  => 'Important',
            self::GRAVITY_MAXIMUM    => 'Maximal',
        ];
    }

    /**
     * Get keys of the Gravities array.
     *
     * @return array
     */
    public static function getGravitiesKeys()
    {
        return \array_keys(self::getGravities());
    }
}
