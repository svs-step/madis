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

class ViolationImpactDictionary extends SimpleDictionary
{
    const IMPACT_LOSS_CONTROL_PERSONAL_DATA        = 'loss_control_personal_data';
    const IMPACT_LIMITATION_RIGHT                  = 'limitation_right';
    const IMPACT_DISCRIMINATION                    = 'discrimination';
    const IMPACT_IDENTITY_THEFT                    = 'identity_theft';
    const IMPACT_FRAUD                             = 'fraud';
    const IMPACT_UNAUTHORIZED_PSEUDO_LIFTING       = 'unauthorized_pseudo_lifting';
    const IMPACT_FINANCIAL_LOSSES                  = 'financial_losses';
    const IMPACT_REPUTATION_DAMAGE                 = 'reputation_damage';
    const IMPACT_LOSS_PROFESSIONAL_CONFIDENTIALITY = 'loss_professional_confidentiality';
    const IMPACT_OTHER                             = 'other';

    public function __construct()
    {
        parent::__construct('registry_violation_impact', self::getImpacts());
    }

    /**
     * Get an array of Impacts.
     *
     * @return array
     */
    public static function getImpacts()
    {
        return [
            self::IMPACT_LOSS_CONTROL_PERSONAL_DATA        => 'Perte de contrôle sur leurs données personnelles',
            self::IMPACT_LIMITATION_RIGHT                  => 'Limitation de leurs droits',
            self::IMPACT_DISCRIMINATION                    => 'Discrimination',
            self::IMPACT_IDENTITY_THEFT                    => 'Vol d\'identité',
            self::IMPACT_FRAUD                             => 'Fraude',
            self::IMPACT_UNAUTHORIZED_PSEUDO_LIFTING       => 'Levée non autorisée de pseudonimisation',
            self::IMPACT_FINANCIAL_LOSSES                  => 'Pertes financières',
            self::IMPACT_REPUTATION_DAMAGE                 => 'Atteinte à la réputation',
            self::IMPACT_LOSS_PROFESSIONAL_CONFIDENTIALITY => 'Perte de la confidentialité de données protégées par un secret professionnel',
            self::IMPACT_OTHER                             => 'Autre',
        ];
    }

    /**
     * Get keys of the Impacts array.
     *
     * @return array
     */
    public static function getImpactsKeys()
    {
        return \array_keys(self::getImpacts());
    }
}
