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

namespace App\Tests\Domain\Registry\Symfony\EventSubscriber\Doctrine;

use App\Domain\Registry\Calculator\Completion\ConformiteTraitementCompletion;
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Symfony\EventSubscriber\Doctrine\MesurementSubscriber;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

class MesurementSubscriberTest extends TestCase
{
    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgs;

    /**
     * @var ConformiteTraitementCompletion
     */
    private $conformiteTraitementCompletion;

    /**
     * @var MesurementSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->lifeCycleEventArgs = $this->prophesize(LifecycleEventArgs::class);

        $this->subscriber = new MesurementSubscriber();
    }

    /**
     * Test instance of Subscriber.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf(EventSubscriber::class, $this->subscriber);
    }

    /**
     * Test getSubscribedEvents of current subscriber.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [
                'postUpdate',
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testPostUpdateWithAppliedStatus()
    {
        $object        = $this->prophesize(Mesurement::class);
        $reponse       = $this->prophesize(Reponse::class);
        $objectManager = $this->prophesize(ObjectManager::class);

        $object->getStatus()->shouldBeCalled()->willReturn(MesurementStatusDictionary::STATUS_APPLIED);
        $object->getConformiteTraitementReponses()->shouldBeCalled()->willReturn([$reponse->reveal()]);

        $this->lifeCycleEventArgs->getObject()->shouldBeCalled()->willReturn($object->reveal());
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($objectManager->reveal());

        $reponse->addActionProtectionsPlanifiedNotSeen($object->reveal())->shouldBeCalled();
        $reponse->removeActionProtection($object->reveal())->shouldBeCalled();

        $objectManager->persist($reponse->reveal())->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->subscriber->postUpdate($this->lifeCycleEventArgs->reveal());
    }

    public function testPostUpdateWithNotAppliedStatus()
    {
        $object        = $this->prophesize(Mesurement::class);
        $reponse       = $this->prophesize(Reponse::class);
        $objectManager = $this->prophesize(ObjectManager::class);

        $object->getStatus()->shouldBeCalled()->willReturn('foo');
        $object->getConformiteTraitementReponses()->shouldBeCalled()->willReturn([$reponse->reveal()]);

        $this->lifeCycleEventArgs->getObject()->shouldBeCalled()->willReturn($object->reveal());
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($objectManager->reveal());

        $reponse->removeActionProtectionsPlanifiedNotSeen($object->reveal())->shouldBeCalled();

        $objectManager->persist($reponse->reveal())->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->subscriber->postUpdate($this->lifeCycleEventArgs->reveal());
    }
}
