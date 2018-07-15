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

class MesurementTypeDictionary extends SimpleDictionary
{
    const TYPE_TECHNICAL      = 'technical';
    const TYPE_ORGANISATIONAL = 'organisational';
    const TYPE_LEGAL          = 'legal';

    public function __construct()
    {
        parent::__construct('registry_mesurement_type', self::getTypes());
    }

    /**
     * Get an array of Types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_LEGAL          => 'Juridique',
            self::TYPE_ORGANISATIONAL => 'Organisationnel',
            self::TYPE_TECHNICAL      => 'Technique',
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
