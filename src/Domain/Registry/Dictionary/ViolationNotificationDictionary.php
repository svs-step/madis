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

class ViolationNotificationDictionary extends SimpleDictionary
{
    const NOTIFICATION_CROSS_BORDER = 'cross_border';
    const NOTIFICATION_CNIL         = 'cnil';
    const NOTIFICATION_OTHER        = 'other';

    public function __construct()
    {
        parent::__construct('registry_violation_notification', self::getNotifications());
    }

    /**
     * Get an array of Notifications.
     *
     * @return array
     */
    public static function getNotifications()
    {
        return [
            self::NOTIFICATION_CROSS_BORDER => 'Cette notification concerne un traitement transfrontalier ciblant des personnes de différents états membres',
            self::NOTIFICATION_CNIL         => 'La violation a ou va être notifiée à la CNIL',
            self::NOTIFICATION_OTHER        => 'La violation a ou va être notifiée à une autre autorité en charge de la protection des données',
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
