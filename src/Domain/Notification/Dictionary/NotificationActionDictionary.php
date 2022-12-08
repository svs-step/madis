<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author <chayrouse@datakode.fr>
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

class NotificationActionDictionary extends SimpleDictionary
{
    public const CREATE = 'create';
    public const ADD    = 'add';
    public const EDIT   = 'edit';
    public const DELETE = 'delete';
    public const LATE   = 'late';

    public function __construct()
    {
        parent::__construct('notifications_notification_action', self::getActions());
    }

    /**
     * Get an array of Objects.
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            self::CREATE => 'Création',
            self::ADD    => 'Ajout',
            self::EDIT   => 'Modification',
            self::DELETE => 'Suppression',
            self::LATE   => 'Retard (automatique)',
        ];
    }

    /**
     * Get keys of the Objects array.
     *
     * @return array
     */
    public static function getActionsKeys()
    {
        return \array_keys(self::getActions());
    }
}
