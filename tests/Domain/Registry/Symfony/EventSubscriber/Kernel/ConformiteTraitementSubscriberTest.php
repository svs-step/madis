<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Tests\Domain\Registry\Symfony\EventSubscriber\Kernel;

use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Registry\Symfony\EventSubscriber\Event\ConformiteTraitementEvent;
use App\Domain\Registry\Symfony\EventSubscriber\Kernel\ConformiteTraitementSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConformiteTraitementSubscriberTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ConformiteTraitementSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);

        $this->subscriber = new ConformiteTraitementSubscriber($this->entityManager->reveal());
    }

    /**
     * Test instance of Subscriber.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    /**
     * Test getSubscribedEvents of current subscriber.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [
                ConformiteTraitementEvent::class => ['resetAllReponseMesurementNotSeen'],
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testResetAllReponseMesurementNotSeen()
    {
        $conformiteTraitement = $this->prophesize(ConformiteTraitement::class);
        $event                = new ConformiteTraitementEvent($conformiteTraitement->reveal());
        $reponse              = $this->prophesize(Reponse::class);

        $conformiteTraitement->getReponses()->shouldBeCalled()->willReturn([$reponse->reveal()]);

        $reponse->resetActionProtectionsPlanifiedNotSeens()->shouldBeCalled();

        $this->entityManager->persist($reponse->reveal())->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->subscriber->resetAllReponseMesurementNotSeen($event);
    }
}
