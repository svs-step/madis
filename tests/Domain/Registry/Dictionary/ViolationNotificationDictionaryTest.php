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

namespace App\Tests\Domain\Registry\Dictionary;

use App\Domain\Registry\Dictionary\ViolationNotificationDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ViolationNotificationDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationNotificationDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationNotificationDictionary();

        $this->assertEquals('registry_violation_notification', $dictionary->getName());
        $this->assertEquals(ViolationNotificationDictionary::getNotifications(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationNotificationDictionary::NOTIFICATION_CROSS_BORDER => 'Cette notification concerne un traitement transfrontalier ciblant des personnes de différents états membres',
            ViolationNotificationDictionary::NOTIFICATION_CNIL         => 'La violation a ou va être notifiée à la CNIL',
            ViolationNotificationDictionary::NOTIFICATION_OTHER        => 'La violation a ou va être notifiée à une autre autorité en charge de la protection des données',
        ];

        $this->assertEquals($data, ViolationNotificationDictionary::getNotifications());
        $this->assertEquals(
            \array_keys(ViolationNotificationDictionary::getNotifications()),
            ViolationNotificationDictionary::getNotificationsKeys()
        );
    }
}
