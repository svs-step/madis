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

class DelayPeriodDictionary extends SimpleDictionary
{
    const PERIOD_DAY   = 'day';
    const PERIOD_MONTH = 'month';
    const PERIOD_YEAR  = 'year';

    public function __construct()
    {
        parent::__construct('registry_delay_period', self::getPeriods());
    }

    /**
     * Get an array of Types.
     *
     * @return array
     */
    public static function getPeriods()
    {
        return [
            self::PERIOD_DAY   => 'Jour(s)',
            self::PERIOD_MONTH => 'Mois',
            self::PERIOD_YEAR  => 'An(s)',
        ];
    }

    /**
     * Get keys of the Types array.
     *
     * @return array
     */
    public static function getPeriodsKeys()
    {
        return \array_keys(self::getPeriods());
    }
}
