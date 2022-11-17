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

namespace App\Domain\Notification\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

class NotificationMailParametersFrequencyDictionary extends SimpleDictionary
{
    const NONE         = 'NONE';
    const ONLY_ONE     = 'ONLY_ONE';
    const HOURLY       = 'HOURLY';
    const DAYLY        = 'DAYLY';
    const WEEKLY       = 'WEEKLY';
    const MONTHLY      = 'MONTHLY';

    public function __construct()
    {
        parent::__construct('notificationMailParametersFrequency', self::getFrequencies());
    }

    /**
     * Get an array of Roles.
     *
     * @return array
     */
    public static function getFrequencies()
    {
        return [
            self::NONE         => 'Aucune notification',
            self::ONLY_ONE     => 'Notification unitaire',
            self::HOURLY       => 'Toutes les heures',
            self::DAYLY        => 'Quotidien',
            self::WEEKLY       => 'Hebdomadaire',
            self::MONTHLY      => 'Mensuelle',
        ];
    }

    /**
     * Get keys of the Roles array.
     *
     * @return array
     */
    public static function getFrequenciesKeys()
    {
        return \array_keys(self::getFrequencies());
    }
}
