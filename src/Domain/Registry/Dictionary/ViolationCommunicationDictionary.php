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

class ViolationCommunicationDictionary extends SimpleDictionary
{
    const YES  = 'yes';
    const SOON = 'soon';
    const NO   = 'no';

    public function __construct()
    {
        parent::__construct('registry_violation_communication', self::getCommunications());
    }

    /**
     * Get an array of Communications.
     *
     * @return array
     */
    public static function getCommunications()
    {
        return [
            self::YES  => 'Oui, les personnes ont été informées',
            self::SOON => 'Non, mais elles le seront',
            self::NO   => 'Non ils ne le seront pas',
        ];
    }

    /**
     * Get keys of the Communications array.
     *
     * @return array
     */
    public static function getCommunicationsKeys()
    {
        return \array_keys(self::getCommunications());
    }
}
