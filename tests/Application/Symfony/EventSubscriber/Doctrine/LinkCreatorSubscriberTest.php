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

use App\Application\Symfony\EventSubscriber\Doctrine\LinkCreatorSubscriber;
use App\Application\Symfony\Security\UserProvider;
use App\Application\Traits\Model\CreatorTrait;
use App\Domain\User\Model;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

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
     * @var bool
     */
    private $linkAdmin;

    /**
     * @var LinkCreatorSubscriber
     */
    private $sut;

    protected function setUp(): void
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);
        $this->userProviderProphecy       = $this->prophesize(UserProvider::class);

        parent::setUp();
    }

    protected function getSut(bool $linkAdmin = false): LinkCreatorSubscriber
    {
        return new LinkCreatorSubscriber(
            $this->userProviderProphecy->reveal(),
            $linkAdmin
        );
    }

    /**
     * Test instance of Subscriber.
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(EventSubscriber::class, $this->getSut());
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
            $this->getSut()->getSubscribedEvents()
        );
    }

    /**
     * Test prePersist
     * Object has no CreatorTrait trait.
     *
     * @throws \Exception
     */
    public function testPrePersistNoCreatorTrait(): void
    {
        $object = new class() {
        };
        $objectUses = \class_uses($object);
        $user       = new Model\User();
        $linkAdmin  = false;

        $tokenProphecy = $this->prophesize(TokenInterface::class);

        $tokenProphecy->getUser()->shouldBeCalled()->willReturn($user);
        $this->userProviderProphecy->getToken()->shouldBeCalled()->willReturn($tokenProphecy->reveal());
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertFalse(\in_array(CreatorTrait::class, $objectUses));
        $this->getSut($linkAdmin)->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        // This test must only not return error
    }

    /**
     * Test prePersist
     * Object has CreatorTrait but user is not a User class.
     *
     * @throws \Exception
     */
    public function testPrePersistNoAuthenticatedUser(): void
    {
        $object     = new DummyLinkCreatorSubscriberTest();
        $objectUses = \class_uses($object);
        $user       = 'anon.';
        $linkAdmin  = false;

        $tokenProphecy = $this->prophesize(TokenInterface::class);

        $tokenProphecy->getUser()->shouldBeCalled()->willReturn($user);
        $this->userProviderProphecy->getToken()->shouldBeCalled()->willReturn($tokenProphecy->reveal());
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertTrue(\in_array(CreatorTrait::class, $objectUses));
        $this->assertFalse($user instanceof Model\User);
        $this->getSut($linkAdmin)->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertNull($object->getCreator());
    }

    /**
     * Test prePersist
     * Object already has creator, then the connected user is not auto linked.
     */
    public function testPrePersistUserAlreadySet(): void
    {
        $object            = new DummyLinkCreatorSubscriberTest();
        $objectUses        = \class_uses($object);
        $alreadyLinkedUser = new Model\User();
        $object->setCreator($alreadyLinkedUser);
        $loggerUser = new Model\User();
        $linkAdmin  = false;

        $tokenProphecy = $this->prophesize(TokenInterface::class);

        $tokenProphecy->getUser()->shouldBeCalled()->willReturn($loggerUser);
        $this->userProviderProphecy->getToken()->shouldBeCalled()->willReturn($tokenProphecy->reveal());
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertTrue(\in_array(CreatorTrait::class, $objectUses));
        $this->assertEquals($alreadyLinkedUser, $object->getCreator());
        $this->getSut($linkAdmin)->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertEquals($alreadyLinkedUser, $object->getCreator());
    }

    /**
     * Test prePersist
     * Don't link admin as creator.
     *
     * @throws \Exception
     */
    public function testPrePersistNoLinkAdmin(): void
    {
        $object     = new DummyLinkCreatorSubscriberTest();
        $objectUses = \class_uses($object);
        $user       = new Model\User();
        $linkAdmin  = false;

        $tokenProphecy = $this->prophesize(TokenInterface::class);

        $tokenProphecy->getUser()->shouldBeCalled()->willReturn($user);
        $this->userProviderProphecy->getToken()->shouldBeCalled()->willReturn($tokenProphecy->reveal());
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertTrue(\in_array(CreatorTrait::class, $objectUses));
        $this->assertTrue($user instanceof Model\User);
        $this->getSut($linkAdmin)->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertEquals($user, $object->getCreator());
    }

    /**
     * Test prePersist
     * Link admin as creator.
     *
     * @throws \Exception
     */
    public function testPrePersistLinkAdmin(): void
    {
        $object     = new DummyLinkCreatorSubscriberTest();
        $objectUses = \class_uses($object);
        $user       = new Model\User();
        $admin      = new Model\User();
        $linkAdmin  = true;

        $sourceTokenProphecy = $this->prophesize(TokenInterface::class);
        $sourceTokenProphecy->getUser()->shouldBeCalled()->willReturn($admin);
        $tokenProphecy = $this->prophesize(TokenInterface::class);

        $switchUserRole = new SwitchUserRole('ROLE_PREVIOUS_ADMIN', $sourceTokenProphecy->reveal());

        $tokenProphecy->getUser()->shouldBeCalled()->willReturn($user);
        $tokenProphecy->getRoles()->shouldBeCalled()->willReturn([$switchUserRole]);

        $this->userProviderProphecy->getToken()->shouldBeCalled()->willReturn($tokenProphecy->reveal());
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertTrue(\in_array(CreatorTrait::class, $objectUses));
        $this->assertTrue($user instanceof Model\User);

        $this->getSut($linkAdmin)->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertEquals($admin, $object->getCreator());
    }

    /**
     * Test prePersist
     * Link admin as creator but there is no SwitchUserRole, then connected user is linked by default.
     *
     * @throws \Exception
     */
    public function testPrePersistLinkAdminNoSwitchUserRole(): void
    {
        $object     = new DummyLinkCreatorSubscriberTest();
        $objectUses = \class_uses($object);
        $user       = new Model\User();
        $linkAdmin  = true;

        $tokenProphecy = $this->prophesize(TokenInterface::class);

        $role = new Role('ROLE_ADMIN');

        $tokenProphecy->getUser()->shouldBeCalled()->willReturn($user);
        $tokenProphecy->getRoles()->shouldBeCalled()->willReturn([$role]);

        $this->userProviderProphecy->getToken()->shouldBeCalled()->willReturn($tokenProphecy->reveal());
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->assertTrue(\in_array(CreatorTrait::class, $objectUses));
        $this->assertTrue($user instanceof Model\User);

        $this->getSut($linkAdmin)->prePersist($this->lifeCycleEventArgsProphecy->reveal());
        $this->assertEquals($user, $object->getCreator());
    }
}

class DummyLinkCreatorSubscriberTest
{
    use CreatorTrait;
}
