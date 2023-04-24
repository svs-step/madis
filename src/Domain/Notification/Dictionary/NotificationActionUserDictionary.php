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

class NotificationActionUserDictionary extends SimpleDictionary
{
    public const ADD                  = 'add';
    public const AUTOMATIC            = 'automatic';
    public const LATE_SURVEY          = 'late_survey';
    public const NO_LOGIN             = 'no_login';
    public const VALIDATION           = 'validation';
    public const TREATMENT_NEEDS_AIPD = 'treatment_needs_aipd';

    public function __construct()
    {
        parent::__construct('notificationActionUser', self::getActions());
    }

    /**
     * Get an array of Roles.
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            self::ADD                  => 'Ajout',
            self::TREATMENT_NEEDS_AIPD => 'AIPD nécéssaire',
            self::VALIDATION           => 'Validation nécéssaire',
            self::AUTOMATIC            => 'Rappel automatique',
        ];
    }

    /**
     * Get keys of the Roles array.
     *
     * @return array
     */
    public static function getFrequenciesKeys()
    {
        return \array_keys(self::getActions());
    }
}
