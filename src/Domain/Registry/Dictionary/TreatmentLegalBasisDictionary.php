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

class TreatmentLegalBasisDictionary extends SimpleDictionary
{
    public const BASE_NONE                    = 'to-be-decided';
    public const BASE_CONSENT                 = 'consent';
    public const BASE_LEGAL_OBLIGATION        = 'legal-obligation';
    public const BASE_CONTRACT_EXECUTION      = 'contract-execution';
    public const BASE_PUBLIC_INTEREST_MISSION = 'public-interest-mission';
    public const BASE_LEGITIMATE_INTEREST     = 'legitimate-interest';
    public const BASE_VITAL_INTEREST          = 'vital-interest';

    public function __construct()
    {
        parent::__construct('registry_treatment_legal_basis', self::getBasis());
    }

    /**
     * Get an array of Basis.
     *
     * @return array
     */
    public static function getBasis()
    {
        return [
            self::BASE_NONE                    => 'À déterminer',
            self::BASE_CONSENT                 => 'Le consentement',
            self::BASE_LEGAL_OBLIGATION        => 'L\'obligation légale',
            self::BASE_CONTRACT_EXECUTION      => 'L\'exécution d\'un contrat',
            self::BASE_PUBLIC_INTEREST_MISSION => 'L\'exécution d\'une mission d\'intérêt public',
            self::BASE_LEGITIMATE_INTEREST     => 'L\'intérêt légitime',
            self::BASE_VITAL_INTEREST          => 'L\'intérêt vital',
        ];
    }

    /**
     * Get keys of the Basis array.
     *
     * @return array
     */
    public static function getBasisKeys()
    {
        return \array_keys(self::getBasis());
    }
}
