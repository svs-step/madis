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

class TreatmentConcernedPeopleDictionary extends SimpleDictionary
{
    const TYPE_PARTICULAR = 'particular';
    const TYPE_USER       = 'user';
    const TYPE_AGENT      = 'agent';
    const TYPE_ELECTED    = 'elected';
    const TYPE_COMPANY    = 'company';
    const TYPE_PARTNER    = 'partner';

    public function __construct()
    {
        parent::__construct('registry_treatment_concerned_people', self::getTypes());
    }

    /**
     * Get an array of Types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_PARTICULAR => 'Particuliers',
            self::TYPE_USER       => 'Internautes',
            self::TYPE_AGENT      => 'Agents',
            self::TYPE_ELECTED    => 'Élus',
            self::TYPE_COMPANY    => 'Entreprises',
            self::TYPE_PARTNER    => 'Partenaires',
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
