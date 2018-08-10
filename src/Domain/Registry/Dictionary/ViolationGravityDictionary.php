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

class ViolationGravityDictionary extends SimpleDictionary
{
    const GRAVITY_NEGLIGIBLE = 'negligible';
    const GRAVITY_LIMITED    = 'limited';
    const GRAVITY_IMPORTANT  = 'important';
    const GRAVITY_MAXIMUM    = 'maximum';

    public function __construct()
    {
        parent::__construct('registry_violation_gravity', self::getGravities());
    }

    /**
     * Get an array of Gravities.
     *
     * @return array
     */
    public static function getGravities()
    {
        return [
            self::GRAVITY_NEGLIGIBLE => 'Négligeable',
            self::GRAVITY_LIMITED    => 'Limité',
            self::GRAVITY_IMPORTANT  => 'Important',
            self::GRAVITY_MAXIMUM    => 'Maximal',
        ];
    }

    /**
     * Get keys of the Gravities array.
     *
     * @return array
     */
    public static function getGravitiesKeys()
    {
        return \array_keys(self::getGravities());
    }
}
