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
