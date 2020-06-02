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

class ProofTypeDictionary extends SimpleDictionary
{
    const TYPE_POLICY_MANAGEMENT        = 'policy_management';
    const TYPE_POLICY_PROTECTION        = 'policy_protection';
    const TYPE_CONCERNED_PEOPLE_REQUEST = 'concerned_people_request';
    const TYPE_MESUREMENT               = 'mesurement';
    const TYPE_CERTIFICATION            = 'certification';
    const TYPE_IT_CHARTER               = 'it_charter';
    const TYPE_DELIBERATION             = 'deliberation';
    const TYPE_CONTRACT                 = 'contract';
    const TYPE_SENSITIZATION            = 'sensitization';
    const TYPE_BALANCE_SHEET            = 'balance_sheet';
    const TYPE_OTHER                    = 'other';

    public function __construct()
    {
        parent::__construct('registry_proof_type', self::getTypes());
    }

    /**
     * Get an array of Types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_POLICY_MANAGEMENT        => 'Politique de Gestion',
            self::TYPE_POLICY_PROTECTION        => 'Politique de Protection',
            self::TYPE_CONCERNED_PEOPLE_REQUEST => 'Demande de personnes concernées',
            self::TYPE_MESUREMENT               => 'Actions de protection',
            self::TYPE_CERTIFICATION            => 'Attestations',
            self::TYPE_IT_CHARTER               => 'Charte informatique',
            self::TYPE_DELIBERATION             => 'Délibération',
            self::TYPE_CONTRACT                 => 'Contrat',
            self::TYPE_SENSITIZATION            => 'Sensibilisation',
            self::TYPE_BALANCE_SHEET            => 'Bilan',
            self::TYPE_OTHER                    => 'Autre',
        ];
    }

    /**
     * Get keys of the Types array.
     *
     * @return array
     */
    public static function getTypesKeys()
    {
        return \array_keys(self::getTypes());
    }
}
