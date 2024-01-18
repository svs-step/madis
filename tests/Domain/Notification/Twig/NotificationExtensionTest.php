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

namespace App\Tests\Domain\Notification\Twig;

use _PHPStan_76800bfb5\Nette\Utils\DateTime;
use App\Domain\Notification\Model;
use App\Domain\Notification\Twig\Extension\NotificationExtension;
use App\Domain\Registry\Model\Violation;
use App\Domain\User\Model\Collectivity;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Persistence\ManagerRegistry;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationExtensionTest
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var TranslatorInterface
     */
    private $translatorProphecy;

    /**
     * @var NotificationExtension
     */
    private $extension;

    public function setUp(): void
    {
        $this->registryProphecy   = $this->prophesize(ManagerRegistry::class);
        $this->securityProphecy   = $this->prophesize(Security::class);
        $this->translatorProphecy = $this->prophesize(TranslatorInterface::class);

        $this->translatorProphecy->trans('notification.modules.treatment')->willReturn('Traitements');
        $this->translatorProphecy->trans('notification.modules.violation')->willReturn('Violations');
        $this->translatorProphecy->trans('notification.actions.create')->willReturn('Création');
        $this->translatorProphecy->trans('notifications.label.de')->willReturn('de');
        $this->translatorProphecy->trans('notifications.label.par')->willReturn('par');
        $this->translatorProphecy->trans('notifications.label.du')->willReturn('du');

        $this->extension = new NotificationExtension(
            $this->translatorProphecy->reveal(),
        );
    }

    /**
     * Test if repo has good heritage.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(NotificationExtension::class, $this->extension);
    }

    public function testGetSentenceForTreatment()
    {
        $col = new Collectivity();
        $col->setName('collectivity');
        $notif = new Model\Notification();
        $notif->setName('Traitement 1');
        $notif->setModule('notification.modules.treatment');
        $notif->setAction('notification.actions.create');

        $notif->setCollectivity($col);
        $sentence = $this->invokeMethod($this->extension, 'getSentence', [$notif]);

        $this->assertEquals('[Traitements] Création de Traitement 1 par collectivity', $sentence);
    }

    public function testGetSentenceForViolation()
    {
        $col = new Collectivity();
        $col->setName('collectivity');
        $notif = new Model\Notification();
        $notif->setName('Violation 1');
        $notif->setModule('notification.modules.violation');
        $notif->setAction('notification.actions.create');
        $violation = new Violation();
        $date      = DateTime::from(time());
        $violation->setDate($date);
        $notif->setObject($violation);

        $notif->setCollectivity($col);
        $sentence = $this->invokeMethod($this->extension, 'getSentence', [$notif]);

        $this->assertEquals('[Violations] Création de Violation 1 du ' . $date->format('d/m/Y') . ' par collectivity', $sentence);
    }
}
