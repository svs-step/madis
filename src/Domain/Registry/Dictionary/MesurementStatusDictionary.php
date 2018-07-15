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

class MesurementStatusDictionary extends SimpleDictionary
{
    const STATUS_APPLIED        = 'applied';
    const STATUS_NOT_APPLIED    = 'not-applied';
    const STATUS_NOT_APPLICABLE = 'not-applicable';

    public function __construct()
    {
        parent::__construct('registry_mesurement_status', self::getStatus());
    }

    /**
     * Get an array of Status.
     *
     * @return array
     */
    public static function getStatus()
    {
        return [
            self::STATUS_APPLIED        => 'Appliquée',
            self::STATUS_NOT_APPLIED    => 'Non appliquée',
            self::STATUS_NOT_APPLICABLE => 'Non applicable',
        ];
    }

    /**
     * Get keys of the Status array.
     *
     * @return array
     */
    public static function getStatusKeys()
    {
        return \array_keys(self::getStatus());
    }
}
