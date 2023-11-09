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

class TreatmentUltimateFateDictionary extends SimpleDictionary
{
    public const FATE_DESTRUCTION  = 'destruction';
    public const FATE_CONSERVATION = 'conservation';
    public const FATE_DEPOSIT      = 'deposit';
    public const FATE_SORT         = 'sort';
    public const FATE_UNKNOWN      = 'unknown';
    public const FATE_NULL         = null;

    public function __construct()
    {
        parent::__construct('registry_treatment_ultimate_fate', self::getUltimateFates());
    }

    /**
     * Get an array of Basis.
     *
     * @return array
     */
    public static function getUltimateFates()
    {
        return [
            self::FATE_DESTRUCTION  => 'Destruction',
            self::FATE_CONSERVATION => 'Conservation',
            self::FATE_DEPOSIT      => 'Versement',
            self::FATE_SORT         => 'Tri',
            self::FATE_UNKNOWN      => 'Non déterminé',
            self::FATE_NULL         => 'Non déterminé',
        ];
    }

    /**
     * Get keys of the Basis array.
     *
     * @return array
     */
    public static function getUltimateFatesKeys()
    {
        return \array_keys(self::getUltimateFates());
    }
}
