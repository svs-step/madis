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

class ViolationNatureDictionary extends SimpleDictionary
{
    const NATURE_CONFIDENTIALITY = 'confidentiality';
    const NATURE_INTEGRITY       = 'integrity';
    const NATURE_AVAILABILITY    = 'availability';

    public function __construct()
    {
        parent::__construct('registry_violation_nature', self::getNatures());
    }

    /**
     * Get an array of Natures.
     *
     * @return array
     */
    public static function getNatures()
    {
        return [
            self::NATURE_CONFIDENTIALITY => 'Perte de la confidentialité',
            self::NATURE_INTEGRITY       => 'Perte de l\'intégrité',
            self::NATURE_AVAILABILITY    => 'Perte de la disponibilité',
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
