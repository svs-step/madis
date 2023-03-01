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

class ViolationNotificationListDictionary extends SimpleDictionary
{
    public const NOTIFICATION_NOTHING = '';
    public const NOTIFICATION_CROSS_BORDER = 'cross_border';
    public const NOTIFICATION_CNIL         = 'cnil';
    public const NOTIFICATION_OTHER        = 'other';

    public function __construct()
    {
        parent::__construct('registry_violation_notification_list', self::getNotificationsList());
    }

    /**
     * Get an array of Notifications.
     *
     * @return array
     */
    public static function getNotificationsList()
    {
        return [
            self::NOTIFICATION_NOTHING => 'Aucune notification',
            self::NOTIFICATION_CROSS_BORDER => 'Notification transfrontalière',
            self::NOTIFICATION_CNIL         => 'Notification à la CNIL',
            self::NOTIFICATION_OTHER        => 'Notification à une autre autorité',
        ];
    }

    /**
     * Get keys of the Notifications array.
     *
     * @return array
     */
    public static function getNotificationsKeys()
    {
        return \array_keys(self::getNotifications());
    }
}
