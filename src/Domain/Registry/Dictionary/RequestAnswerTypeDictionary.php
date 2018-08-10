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

class RequestAnswerTypeDictionary extends SimpleDictionary
{
    const TYPE_MAIL   = 'mail';
    const TYPE_POSTAL = 'postal';
    const TYPE_DIRECT = 'direct';

    public function __construct()
    {
        parent::__construct('registry_request_answer_type', self::getTypes());
    }

    /**
     * Get an array of Answer types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_MAIL   => 'Mail',
            self::TYPE_POSTAL => 'Courrier postal',
            self::TYPE_DIRECT => 'Remis en main propre',
        ];
    }

    /**
     * Get keys of the Answer types array.
     *
     * @return array
     */
    public static function getTypesKeys()
    {
        return \array_keys(self::getTypes());
    }
}
