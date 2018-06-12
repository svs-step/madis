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

namespace App\Domain\User\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

class ContactCivilityDictionary extends SimpleDictionary
{
    const CIVILITY_MISS   = 'mme';
    const CIVILITY_MISTER = 'm';

    public function __construct()
    {
        parent::__construct('user_contact_civility', self::getCivilities());
    }

    /**
     * Get an array of Civilities.
     *
     * @return array
     */
    public static function getCivilities()
    {
        return [
            self::CIVILITY_MISS   => 'Madame',
            self::CIVILITY_MISTER => 'Monsieur',
        ];
    }

    /**
     * Get keys of the Civility array.
     *
     * @return array
     */
    public static function getCivilitiesKeys()
    {
        return \array_keys(self::getCivilities());
    }
}
