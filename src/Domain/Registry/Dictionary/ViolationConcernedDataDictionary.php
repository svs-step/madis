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

class ViolationConcernedDataDictionary extends SimpleDictionary
{
    public const DATA_CIVIL_STATUS                  = 'civil_status';
    public const DATA_SOCIAL_NUMBER                 = 'social_number';
    public const DATA_CONTACT                       = 'contact';
    public const DATA_IDENTIFICATION_ACCESS         = 'indentification_access';
    public const DATA_FINANCIAL                     = 'financial';
    public const DATA_OFFICIAL_DOCUMENT             = 'official_document';
    public const DATA_LOCATION                      = 'location';
    public const DATA_OFFENSES_CONVICTIONS_SECURITY = 'offenses_convictions_security';
    public const DATA_UNKNOWN                       = 'unknown';
    public const DATA_RACIAL_ETHNIC                 = 'racial_ethnic';
    public const DATA_POLITICAL                     = 'political';
    public const DATA_PHILOSOPHICAL_RELIGIOUS       = 'philosophical_religious';
    public const DATA_TRADE_UNION_MEMBERSHIP        = 'trade_union_membership';
    public const DATA_SEXUAL_ORIENTATION            = 'sexual_orientation';
    public const DATA_HEALTH                        = 'health';
    public const DATA_BIOMETRIC                     = 'biometric';
    public const DATA_GENETIC                       = 'genetic';
    public const DATA_OTHER                         = 'other';

    public function __construct()
    {
        parent::__construct('registry_violation_concerned_data', self::getConcernedData());
    }

    /**
     * Get an array of Concerned data.
     *
     * @return array
     */
    public static function getConcernedData()
    {
        return [
            self::DATA_CIVIL_STATUS                  => 'État civil (nom, sexe, date de naissance, âge...)',
            self::DATA_SOCIAL_NUMBER                 => 'NIR (Numéro de sécurité sociale)',
            self::DATA_CONTACT                       => 'Coordonnées (adresse postale ou électronique, numéros de téléphone fixe ou portable...)',
            self::DATA_IDENTIFICATION_ACCESS         => 'Données d’identification ou d’accès (identifiant, mot de passe, numéro client...)',
            self::DATA_FINANCIAL                     => 'Données relatives à des informations financières (revenus, numéro de carte de crédit, coordonnées bancaires), économiques',
            self::DATA_OFFICIAL_DOCUMENT             => 'Documents officiels (Passeports, pièces d’identité, etc.)',
            self::DATA_LOCATION                      => 'Données de localisation',
            self::DATA_OFFENSES_CONVICTIONS_SECURITY => 'Données relatives à des infractions, condamnations, mesures de sûreté',
            self::DATA_UNKNOWN                       => 'Les données concernées ne sont pas connues pour le moment',
            self::DATA_RACIAL_ETHNIC                 => 'Origine raciale ou ethnique',
            self::DATA_POLITICAL                     => 'Opinions politiques',
            self::DATA_PHILOSOPHICAL_RELIGIOUS       => 'Opinions philosophiques ou religieuses',
            self::DATA_TRADE_UNION_MEMBERSHIP        => 'Appartenance syndicale',
            self::DATA_SEXUAL_ORIENTATION            => 'Orientation sexuelle',
            self::DATA_HEALTH                        => 'Données de santé',
            self::DATA_BIOMETRIC                     => 'Données biométriques',
            self::DATA_GENETIC                       => 'Données génétiques',
            self::DATA_OTHER                         => 'La violation concerne d\'autres données',
        ];
    }

    /**
     * Get keys of the Concerned data array.
     *
     * @return array
     */
    public static function getConcernedDataKeys()
    {
        return \array_keys(self::getConcernedData());
    }
}
