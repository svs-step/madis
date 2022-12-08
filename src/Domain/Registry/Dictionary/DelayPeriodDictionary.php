<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class DelayPeriodDictionary extends SimpleDictionary
{
    public const PERIOD_DAY   = 'day';
    public const PERIOD_MONTH = 'month';
    public const PERIOD_YEAR  = 'year';

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
