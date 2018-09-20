<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

class ViolationConcernedDataDictionary extends SimpleDictionary
{
    const DATA_CIVIL_STATUS                  = 'civil_status';
    const DATA_SOCIAL_NUMBER                 = 'social_number';
    const DATA_CONTACT                       = 'contact';
    const DATA_IDENTIFICATION_ACCESS         = 'indentification_access';
    const DATA_FINANCIAL                     = 'financial';
    const DATA_OFFICIAL_DOCUMENT             = 'official_document';
    const DATA_LOCATION                      = 'location';
    const DATA_OFFENSES_CONVICTIONS_SECURITY = 'offenses_convictions_security';
    const DATA_UNKNOWN                       = 'unknown';
    const DATA_RACIAL_ETHNIC                 = 'racial_ethnic';
    const DATA_POLITICAL                     = 'political';
    const DATA_PHILOSOPHICAL_RELIGIOUS       = 'philosophical_religious';
    const DATA_TRADE_UNION_MEMBERSHIP        = 'trade_union_membership';
    const DATA_SEXUAL_ORIENTATION            = 'sexual_orientation';
    const DATA_HEALTH                        = 'health';
    const DATA_BIOMETRIC                     = 'biometric';
    const DATA_GENETIC                       = 'genetic';
    const DATA_OTHER                         = 'other';

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
