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
