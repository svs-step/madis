<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

class TreatmentDataCategoryDictionary extends SimpleDictionary
{
    const CATEGORY_CIVILITY                 = 'civility';
    const CATEGORY_POSTAL                   = 'postal';
    const CATEGORY_PHONE                    = 'phone';
    const CATEGORY_EMAIL                    = 'email';
    const CATEGORY_IP                       = 'ip';
    const CATEGORY_GEO                      = 'geo';
    const CATEGORY_PICTURE                  = 'picture';
    const CATEGORY_FAMILY_SITUATION         = 'family-situation';
    const CATEGORY_FILIATION                = 'filiation';
    const CATEGORY_HEALTH                   = 'health';
    const CATEGORY_CHILD                    = 'child';
    const CATEGORY_SOCIAL_NUM               = 'social-security-number';
    const CATEGORY_CAF_NUM                  = 'caf';
    const CATEGORY_SYNDICATE                = 'syndicate';
    const CATEGORY_CONNEXION                = 'connexion';
    const CATEGORY_BANK                     = 'bank';
    const CATEGORY_HERITAGE                 = 'heritage';
    const CATEGORY_EARNING                  = 'earning';
    const CATEGORY_TAX_SITUATION            = 'tax-situation';
    const CATEGORY_BANK_SITUATION           = 'bank-situation';
    const CATEGORY_IDENTITY                 = 'identity';
    const CATEGORY_PUBLIC_RELIGIOUS_OPINION = 'public-religious-opinion';
    const CATEGORY_RACIAL_ETHNIC_OPINION    = 'racial-ethnic-opinion';
    const CATEGORY_SEX_LIFE                 = 'sex-life';

    /*
     Etat civil
o    Coordonnées postales
o    Coordonnées téléphoniques
o    Emails
o    Adresse IP
o    Géolocalisation
o    Photos-vidéos
o    Situation familiale
o    Filiation
o    Santé
o    Enfant de moins de 16 ans
o    Numéro de Sécurité Social
o    Numéro de CAF
o    Appartenance Syndicale
o    Connexion
o    Information bancaire
o    Patrimoine
o    Revenus
o    Situation fiscale
o    Situation bancaire
o    Pièces d’identité
o    Opinion politique ou religieuse
o    Origine raciale ou ethnique
o    Vie sexuelle
     */
    public function __construct()
    {
        parent::__construct('registry_treatment_data_category', self::getCategories());
    }

    /**
     * Get an array of Basis.
     *
     * @return array
     */
    public static function getCategories()
    {
        return [
            self::CATEGORY_CIVILITY                 => 'Etat civil',
            self::CATEGORY_POSTAL                   => 'Coordonnées postales',
            self::CATEGORY_PHONE                    => 'Coordonnées téléphoniques',
            self::CATEGORY_EMAIL                    => 'Emails',
            self::CATEGORY_IP                       => 'Adresse IP',
            self::CATEGORY_GEO                      => 'Géolocalisation',
            self::CATEGORY_PICTURE                  => 'Photos-vidéos',
            self::CATEGORY_FAMILY_SITUATION         => 'Situation familiale',
            self::CATEGORY_FILIATION                => 'Filiation',
            self::CATEGORY_HEALTH                   => 'Santé',
            self::CATEGORY_CHILD                    => 'Enfant de moins de 16 ans',
            self::CATEGORY_SOCIAL_NUM               => 'Numéro de Sécurité Social',
            self::CATEGORY_CAF_NUM                  => 'Numéro de CAF',
            self::CATEGORY_SYNDICATE                => 'Appartenance Syndicale',
            self::CATEGORY_CONNEXION                => 'Connexion',
            self::CATEGORY_BANK                     => 'Information bancaire',
            self::CATEGORY_HERITAGE                 => 'Patrimoine',
            self::CATEGORY_EARNING                  => 'Revenus',
            self::CATEGORY_TAX_SITUATION            => 'Situation fiscale',
            self::CATEGORY_BANK_SITUATION           => 'Situation bancaire',
            self::CATEGORY_IDENTITY                 => 'Pièces d’identité',
            self::CATEGORY_PUBLIC_RELIGIOUS_OPINION => 'Opinion politique ou religieuse',
            self::CATEGORY_RACIAL_ETHNIC_OPINION    => 'Origine raciale ou ethnique',
            self::CATEGORY_SEX_LIFE                 => 'Vie sexuelle',
        ];
    }

    /**
     * Get keys of the Basis array.
     *
     * @return array
     */
    public static function getCategoryKeys()
    {
        return \array_keys(self::getCategories());
    }
}
