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

use App\Domain\Maturity\Symfony\EventSubscriber\Doctrine\GenerateMaturitySubscriber;
use App\Domain\Registry\Calculator\Completion\ConformiteTraitementCompletion;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Symfony\EventSubscriber\Doctrine\ConformiteTraitementCompletionSubscriber;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

class ConformiteTraitementCompletionSubscriberTest extends TestCase
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
     * @var GenerateMaturitySubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->lifeCycleEventArgs             = $this->prophesize(LifecycleEventArgs::class);
        $this->conformiteTraitementCompletion = $this->prophesize(ConformiteTraitementCompletion::class);

        $this->subscriber = new ConformiteTraitementCompletionSubscriber(
            $this->conformiteTraitementCompletion->reveal()
        );
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
                'prePersist',
                'preUpdate',
                'postUpdate',
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    /**
     * Test prePersist.
     */
    public function testPrePersist()
    {
        $object = new ConformiteTraitement();

        $this->lifeCycleEventArgs->getObject()->shouldBeCalled()->willReturn($object);

        $this->conformiteTraitementCompletion->setCalculsConformite($object)->shouldBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgs->reveal());
    }

    /**
     * Test preUpdate.
     */
    public function testPreUpdate()
    {
        $object = new ConformiteTraitement();

        $this->lifeCycleEventArgs->getObject()->shouldBeCalled()->willReturn($object);

        $this->conformiteTraitementCompletion->setCalculsConformite($object)->shouldBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgs->reveal());
    }

    /**
     * Test postUpdate on Reponse.
     */
    public function testPostUpdateWithReponseObject()
    {
        $object        = $this->prophesize(Reponse::class);
        $conformite    = new ConformiteTraitement();
        $objectManager = $this->prophesize(ObjectManager::class);

        $object->getConformiteTraitement()->shouldBeCalled()->willReturn($conformite);

        $this->lifeCycleEventArgs->getObject()->shouldBeCalled()->willReturn($object->reveal());
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($objectManager->reveal());

        $objectManager->persist($conformite)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->conformiteTraitementCompletion->setCalculsConformite($conformite)->shouldBeCalled();

        $this->subscriber->postUpdate($this->lifeCycleEventArgs->reveal());
    }

    /**
     * Test postUpdate on Reponse.
     */
    public function testPostUpdateWithMesurementObject()
    {
        $object        = $this->prophesize(Mesurement::class);
        $reponse       = $this->prophesize(Reponse::class);
        $conformite    = new ConformiteTraitement();
        $objectManager = $this->prophesize(ObjectManager::class);

        $object->getConformiteTraitementReponses()->shouldBeCalled()->willReturn([$reponse]);
        $reponse->getConformiteTraitement()->shouldBeCalled()->willReturn($conformite);

        $this->lifeCycleEventArgs->getObject()->shouldBeCalled()->willReturn($object->reveal());
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($objectManager->reveal());

        $objectManager->persist($conformite)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->conformiteTraitementCompletion->setCalculsConformite($conformite)->shouldBeCalled();

        $this->subscriber->postUpdate($this->lifeCycleEventArgs->reveal());
    }
}
