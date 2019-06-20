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

namespace App\Tests\Domain\Event\Symfony\EventSubscriber\Doctrine;

use App\Application\Symfony\EventSubscriber\Doctrine\LinkCreatorSubscriber;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\Model\CreatorTrait;
use App\Domain\User\Model;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;

class LinkCreatorSubscriberTest extends TestCase
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
     * @var LinkCreatorSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);
        $this->userProviderProphecy       = $this->prophesize(UserProvider::class);

        $this->subscriber = new LinkCreatorSubscriber(
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
     * Object has trait CollectivityTrait.
     *
     * @throws \Exception
     */
    public function testPrePersist()
    {
        $object = new class() {
            use CreatorTrait;
        };

        $userProphecy = $this->prophesize(Model\User::class);
        $this->userProviderProphecy->getAuthenticatedUser()->shouldBeCalled()->willReturn($userProphecy->reveal());

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertNull($object->getCreator());
        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertEquals($userProphecy->reveal(), $object->getCreator());
    }
}
