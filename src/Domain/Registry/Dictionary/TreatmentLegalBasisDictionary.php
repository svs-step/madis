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

class TreatmentLegalBasisDictionary extends SimpleDictionary
{
    const BASE_CONSENT                 = 'consent';
    const BASE_LEGAL_OBLIGATION        = 'legal-obligation';
    const BASE_CONTRACT_EXECUTION      = 'contract-execution';
    const BASE_PUBLIC_INTEREST_MISSION = 'public-interest-mission';
    const BASE_VITAL_INTEREST          = 'vital-interest';

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
            self::BASE_CONSENT                 => 'Le consentement',
            self::BASE_LEGAL_OBLIGATION        => 'L\'obligation légale',
            self::BASE_CONTRACT_EXECUTION      => 'L\'exécution d\'un contrat',
            self::BASE_PUBLIC_INTEREST_MISSION => 'L\'exécution d\'une mission d\'intérêt public',
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
