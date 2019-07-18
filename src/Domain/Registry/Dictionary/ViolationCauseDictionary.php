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

class ViolationCauseDictionary extends SimpleDictionary
{
    const CAUSE_INTERNAL_MALICIOUS  = 'internal_malicious';
    const CAUSE_INTERNAL_ACCIDENTAL = 'internal_accidental';
    const CAUSE_EXTERNAL_MALICIOUS  = 'external_malicious';
    const CAUSE_EXTERNAL_ACCIDENTAL = 'external_accidental';
    const CAUSE_UNKNOWN             = 'unknown';

    public function __construct()
    {
        parent::__construct('registry_violation_cause', self::getNatures());
    }

    /**
     * Get an array of Natures.
     *
     * @return array
     */
    public static function getNatures()
    {
        return [
            self::CAUSE_INTERNAL_MALICIOUS  => 'Acte interne malveillant',
            self::CAUSE_INTERNAL_ACCIDENTAL => 'Acte interne accidentel',
            self::CAUSE_EXTERNAL_MALICIOUS  => 'Acte externe malveillant',
            self::CAUSE_EXTERNAL_ACCIDENTAL => 'Acte externe accidentel',
            self::CAUSE_UNKNOWN             => 'Inconnu',
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
