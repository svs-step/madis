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

class ViolationNatureDictionary extends SimpleDictionary
{
    const NATURE_CONFIDENTIALITY = 'confidentiality';
    const NATURE_INTEGRITY       = 'integrity';
    const NATURE_AVAILABILITY    = 'availability';

    public function __construct()
    {
        parent::__construct('registry_violation_nature', self::getNatures());
    }

    /**
     * Get an array of Natures.
     *
     * @return array
     */
    public static function getNatures()
    {
        return [
            self::NATURE_CONFIDENTIALITY => 'Perte de la confidentialité',
            self::NATURE_INTEGRITY       => 'Perte de l\'intégrité',
            self::NATURE_AVAILABILITY    => 'Perte de la disponibilité',
        ];
    }

    /**
     * Get keys of the Natures array.
     *
     * @return array
     */
    public static function getNaturesKeys()
    {
        return \array_keys(self::getNatures());
    }
}
