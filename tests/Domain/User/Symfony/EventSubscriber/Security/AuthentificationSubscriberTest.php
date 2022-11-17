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

namespace App\Tests\Domain\User\Symfony\EventSubscriber\Security;

use App\Domain\User\Model\User;
use App\Domain\User\Symfony\EventSubscriber\Security\AuthenticationSubscriber;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class AuthentificationSubscriberTest extends TestCase
{
    use ReflectionTrait;
    use ProphecyTrait;

    /**
     * @var RequestStack|ObjectProphecy
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface|ObjectProphecy
     */
    private $entityManager;

    /**
     * @var \App\Domain\User\Repository\User|ObjectProphecy
     */
    private $userRepository;

    /**
     * @var \App\Domain\User\Repository\LoginAttempt|ObjectProphecy
     */
    private $attemptRepository;

    /**
     * @var AuthenticationSubscriber
     */
    private $subscriber;

    public function setUp(): void
    {
        $this->requestStack        = $this->prophesize(RequestStack::class);
        $this->attemptRepository   = $this->prophesize(\App\Domain\User\Repository\LoginAttempt::class);
        $this->userRepository      = $this->prophesize(\App\Domain\User\Repository\User::class);
        $this->eventDispatcher     = $this->prophesize(EventDispatcherInterface::class)->reveal();

        $this->subscriber = new AuthenticationSubscriber(
            $this->requestStack->reveal(),
            $this->attemptRepository->reveal(),
            $this->userRepository->reveal(),
            5,
            $this->eventDispatcher
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
                AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthFailure',
                AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthSuccess',
            ],
            $this->subscriber::getSubscribedEvents()
        );
    }

    public function testItAddLogJournalWhenSwitchUserOn()
    {
        $event        = $this->prophesize(AuthenticationSuccessEvent::class);
        $request      = new Request(['_username' => 'bal@dsf.fg'], [], [], [], [], [
            'REMOTE_ADDR' => '192.168.1.1',
        ]);

        $dispatcher = new EventDispatcher();

        $dispatcher->addSubscriber($this->subscriber);

        $dispatcher->dispatch($event->reveal());
        $user = new User();
        $user->setEmail('a@example.org');

        $token = $this->prophesize(TokenInterface::class);
        $token->setUser($user);

        $event->getAuthenticationToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $token->getUsername()->shouldBeCalled()->willReturn($user->getEmail());

        $this->requestStack->getCurrentRequest()->shouldBeCalled()->willReturn($request);

        $this->subscriber->onAuthSuccess($event->reveal());
    }
}
