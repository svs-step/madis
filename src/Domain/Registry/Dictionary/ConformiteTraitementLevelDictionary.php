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

class ConformiteTraitementLevelDictionary extends SimpleDictionary
{
    const CONFORME                = 'conforme';
    const NON_CONFORMITE_MINEURE  = 'non_conformite_mineure';
    const NON_CONFORMITE_MAJEURE  = 'non_conformite_majeure';

    const HEX_COLOR_CONFORME               = '93D14E';
    const HEX_COLOR_NON_CONFORMITE_MINEURE = 'FEC100';
    const HEX_COLOR_NON_CONFORMITE_MAJEURE = 'C04F4D';

    public function __construct()
    {
        parent::__construct('conformite_traitement_level', self::getConformites());
    }

    /**
     * @return array
     */
    public static function getConformites()
    {
        return [
            self::CONFORME               => 'Conforme',
            self::NON_CONFORMITE_MINEURE => 'Non-conformité mineure',
            self::NON_CONFORMITE_MAJEURE => 'Non-conformité majeure',
        ];
    }

    /**
     * @return array
     */
    public static function getConformiteKeys()
    {
        return \array_keys(self::getConformites());
    }

    public static function getHexaConformitesColors()
    {
        return [
            self::CONFORME               => self::HEX_COLOR_CONFORME,
            self::NON_CONFORMITE_MINEURE => self::HEX_COLOR_NON_CONFORMITE_MINEURE,
            self::NON_CONFORMITE_MAJEURE => self::HEX_COLOR_NON_CONFORMITE_MAJEURE,
        ];
    }

    public static function getRgbConformitesColorsForChartView()
    {
        $rgbColors = [];
        foreach (self::getHexaConformitesColors() as $key => $hex) {
            list($r, $g, $b) = \sscanf($hex, '%02x%02x%02x');
            $rgbColors[$key] = "rgba($r, $g, $b)";
        }

        return $rgbColors;
    }

    public static function getConformitesWeight()
    {
        return [
            self::CONFORME               => 1,
            self::NON_CONFORMITE_MINEURE => 2,
            self::NON_CONFORMITE_MAJEURE => 3,
        ];
    }
}
