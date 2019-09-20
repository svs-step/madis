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

namespace App\Tests\Domain\Event\Symfony\EventSubscriber\Doctrine;

use App\Application\Symfony\EventSubscriber\Doctrine\LinkCollectivitySubscriber;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\Model\CollectivityTrait;
use App\Domain\User\Model;
use App\Domain\User\Model\Collectivity;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;

class LinkCollectivitySubscriberTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var UserProvider
     */
    private $userProviderProphecy;

    /**
     * @var LinkCollectivitySubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);
        $this->userProviderProphecy       = $this->prophesize(UserProvider::class);

        $this->subscriber = new LinkCollectivitySubscriber(
            $this->userProviderProphecy->reveal()
        );
    }

    /**
     * Test instance of Subscriber.
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(EventSubscriber::class, $this->subscriber);
    }

    /**
     * Test getSubscribedEvents of current subscriber.
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            [
                'prePersist',
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    /**
     * Test prePersist
     * Object has no trait CollectivityTrait.
     *
     * @throws \Exception
     */
    public function testPrePersistWithoutCollectivityTrait(): void
    {
        $object = new class() {
        };

        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->setCollectivity()->shouldNotBeCalled();
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }

    /**
     * Test prePersist
     * Object has trait CollectivityTrait and collectivity associated.
     *
     * @throws \Exception
     */
    public function testPrePersistHasTraitAndCollectivity(): void
    {
        $objectCollectivity = new Collectivity();
        $object             = new class() {
            use CollectivityTrait;
        };
        $object->setCollectivity($objectCollectivity);

        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->getCollectivity()->shouldNotBeCalled();
        $this->userProviderProphecy->getAuthenticatedUser()->shouldBeCalled()->willReturn($userProphecy);

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertNotNull($object->getCollectivity());
        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertEquals($objectCollectivity, $object->getCollectivity());
    }

    /**
     * Test prePersist
     * Object has trait CollectivityTrait but no collectivity associated.
     *
     * @throws \Exception
     */
    public function testPrePersist(): void
    {
        $object = new class() {
            use CollectivityTrait;
        };

        $collectivity = new Collectivity();
        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy->getAuthenticatedUser()->shouldBeCalled()->willReturn($userProphecy);

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertNull($object->getCollectivity());
        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertEquals($collectivity, $object->getCollectivity());
    }
}
