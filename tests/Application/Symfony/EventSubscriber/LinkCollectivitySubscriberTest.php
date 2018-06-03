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

namespace App\Tests\Domain\Event\Symfony\EventSubscriber;

use App\Application\Symfony\EventSubscriber\LinkCollectivitySubscriber;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\Model\CollectivityTrait;
use App\Domain\Admin\Model\Collectivity;
use App\Domain\User\Model;
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
    public function testPrePersistWithoutCollectivityTrait()
    {
        $object = new class() {
        };

        $userProphecy = $this->prophesize(Model\User::class);
        // GetCollectivity must not be called since trait isn't present
        $userProphecy->setCollectivity()->shouldNotBeCalled();
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }

    /**
     * Test prePersist
     * Object has trait CollectivityTrait.
     *
     * @throws \Exception
     */
    public function testPrePersist()
    {
        $object = new class() {
            use CollectivityTrait;
        };

        $collectivity = new Collectivity();
        $userProphecy = $this->prophesize(Model\User::class);
        // GetCollectivity must not be called since trait is present
        $userProphecy->getCollectivity()->shouldBeCalled()->willReturn($collectivity);
        $this->userProviderProphecy->getAuthenticatedUser()->shouldBeCalled()->willReturn($userProphecy);

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertNull($object->getCollectivity());
        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertEquals($collectivity, $object->getCollectivity());
    }
}
