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

namespace App\Tests\Domain\User\Symfony\EventSubscriber\Kernel;

use App\Domain\Reporting\Model\LogJournal;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Domain\User\Symfony\EventSubscriber\Kernel\SwitchUserSubscriber;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SwitchUserSubscriberTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var Security|ObjectProphecy
     */
    private $security;

    /**
     * @var EntityManagerInterface|ObjectProphecy
     */
    private $entityManager;

    /**
     * @var \App\Domain\User\Repository\User|ObjectProphecy
     */
    private $userRepository;

    /**
     * @var SwitchUserSubscriber
     */
    private $subscriber;

    public function setUp(): void
    {
        $this->security       = $this->prophesize(Security::class);
        $this->entityManager  = $this->prophesize(EntityManagerInterface::class);
        $this->userRepository = $this->prophesize(\App\Domain\User\Repository\User::class);

        $this->subscriber = new SwitchUserSubscriber(
            $this->security->reveal(),
            $this->entityManager->reveal(),
            $this->userRepository->reveal(),
        );
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
                SecurityEvents::SWITCH_USER => 'onSwitchUser',
            ],
            $this->subscriber::getSubscribedEvents()
        );
    }

    public function testItAddLogJournalWhenSwitchUserOn()
    {
        $event        = $this->prophesize(SwitchUserEvent::class);
        $repository   = $this->prophesize(ObjectRepository::class);
        $request      = new Request(['_switch_user' => 'fooEmailUser']);
        $user         = new User();
        $userReferent = new User();
        $collectivity = new Collectivity();

        $this->security->isGranted(UserRoleDictionary::ROLE_ADMIN)->shouldBeCalled()->willReturn(true);

        $userReferent->setCollectivity($collectivity);
        $userReferent->setFirstName('foo');
        $userReferent->setLastName('bar');
        $userReferent->setEmail('fooEmail');
        $user->setLastName('zer');
        $user->setLastName('rez');
        $event->getRequest()->shouldBeCalled()->willReturn($request);
        $event->getTargetUser()->shouldBeCalled()->willReturn($user);
        $this->security->getUser()->shouldBeCalled()->willReturn($userReferent);

        $this->entityManager->persist(Argument::type(LogJournal::class))->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->subscriber->onSwitchUser($event->reveal());
    }

    public function testItAddLogJournalWhenSwitchUserOff()
    {
        $event        = $this->prophesize(SwitchUserEvent::class);
        $request      = new Request(['_switch_user' => '_exit']);
        $user         = new User();
        $userReferent = new User();
        $collectivity = new Collectivity();

        $userReferent->setCollectivity($collectivity);
        $userReferent->setFirstName('foo');
        $userReferent->setLastName('bar');
        $userReferent->setEmail('fooEmail');
        $user->setLastName('zer');
        $user->setLastName('rez');
        $event->getRequest()->shouldBeCalled()->willReturn($request);
        $event->getTargetUser()->shouldBeCalledTimes(2)->willReturn($userReferent);
        $this->security->getUser()->shouldBeCalledTimes(1)->willReturn($user);

        $this->entityManager->persist(Argument::type(LogJournal::class))->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->subscriber->onSwitchUser($event->reveal());
    }

    public function testItSupportsReturnTrueOnExitRequest()
    {
        $event   = $this->prophesize(SwitchUserEvent::class);
        $request = new Request(['_switch_user' => '_exit']);
        $event->getRequest()->shouldBeCalled()->willReturn($request);
        $this->entityManager->getRepository(User::class)->shouldNotBeCalled();
        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$event->reveal()]));
    }

    public function testItSupportsReturnTrueOnAdminUser()
    {
        $event   = $this->prophesize(SwitchUserEvent::class);
        $request = new Request(['_switch_user' => 'foo']);

        $this->security->isGranted(UserRoleDictionary::ROLE_ADMIN)->shouldBeCalled()->willReturn(true);
        $event->getRequest()->shouldBeCalled()->willReturn($request);
        $this->userRepository->findOneOrNullByEmail(Argument::any())->shouldNotBeCalled();
        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$event->reveal()]));
    }

    public function testItSupportsReturnFalseOnNullUser()
    {
        $event   = $this->prophesize(SwitchUserEvent::class);
        $request = new Request(['_switch_user' => 'foo']);
        $event->getRequest()->shouldBeCalled()->willReturn($request);

        $this->security->isGranted(UserRoleDictionary::ROLE_ADMIN)->shouldBeCalled()->willReturn(false);
        $this->userRepository->findOneOrNullByEmail('foo')->shouldBeCalled()->willReturn(null);
        $this->security->getUser()->shouldNotBeCalled();
        $this->assertFalse($this->invokeMethod($this->subscriber, 'supports', [$event->reveal()]));
    }

    public function testItSupportsReturnFalseOnNonAuthorizedRoles()
    {
        $event   = $this->prophesize(SwitchUserEvent::class);
        $request = new Request(['_switch_user' => 'foo']);
        $user    = new User();
        $user->setRoles([UserRoleDictionary::ROLE_REFERENT]);
        $event->getRequest()->shouldBeCalled()->willReturn($request);

        $this->security->isGranted(UserRoleDictionary::ROLE_ADMIN)->shouldBeCalled()->willReturn(false);
        $this->userRepository->findOneOrNullByEmail('foo')->shouldBeCalled()->willReturn($user);

        $this->security->getUser()->shouldNotBeCalled();
        $this->assertFalse($this->invokeMethod($this->subscriber, 'supports', [$event->reveal()]));
    }

    public function testItSupportsReturnFalseOnEmptyReferedCollectivities()
    {
        $event   = $this->prophesize(SwitchUserEvent::class);
        $request = new Request(['_switch_user' => 'foo']);
        $user    = new User();
        $user->setRoles([UserRoleDictionary::ROLE_USER]);
        $connectedUser = new User();
        $collectivity1 = new Collectivity();
        $collectivity2 = new Collectivity();
        $connectedUser->setCollectivity($collectivity1);
        $connectedUser->setCollectivitesReferees([$collectivity1]);
        $user->setCollectivity($collectivity2);
        $event->getRequest()->shouldBeCalled()->willReturn($request);

        $this->security->isGranted(UserRoleDictionary::ROLE_ADMIN)->shouldBeCalled()->willReturn(false);
        $this->userRepository->findOneOrNullByEmail('foo')->shouldBeCalled()->willReturn($user);

        $this->security->getUser()->shouldBeCalled()->willReturn($connectedUser);
        $this->assertFalse($this->invokeMethod($this->subscriber, 'supports', [$event->reveal()]));
    }

    public function testItSupportsReturnTryeOnAuthorizedReferedCollectivities()
    {
        $event   = $this->prophesize(SwitchUserEvent::class);
        $request = new Request(['_switch_user' => 'foo']);
        $user    = new User();
        $user->setRoles([UserRoleDictionary::ROLE_USER]);
        $connectedUser = new User();
        $collectivity1 = new Collectivity();
        $collectivity2 = new Collectivity();
        $connectedUser->setCollectivity($collectivity1);
        $connectedUser->setCollectivitesReferees([$collectivity1, $collectivity2]);
        $user->setCollectivity($collectivity2);
        $event->getRequest()->shouldBeCalled()->willReturn($request);

        $this->security->isGranted(UserRoleDictionary::ROLE_ADMIN)->shouldBeCalled()->willReturn(false);
        $this->userRepository->findOneOrNullByEmail('foo')->shouldBeCalled()->willReturn($user);

        $this->security->getUser()->shouldBeCalled()->willReturn($connectedUser);
        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$event->reveal()]));
    }
}
