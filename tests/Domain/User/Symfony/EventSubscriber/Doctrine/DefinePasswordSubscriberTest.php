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

namespace App\Tests\Domain\User\Symfony\EventSubscriber\Doctrine;

use App\Domain\User\Component\TokenGenerator;
use App\Domain\User\Model;
use App\Domain\User\Symfony\EventSubscriber\Doctrine\DefinePasswordSubscriber;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class DefinePasswordSubscriberTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var TokenGenerator
     */
    private $tokenGeneratorProphecy;

    /**
     * @var DefinePasswordSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);
        $this->tokenGeneratorProphecy     = $this->prophesize(TokenGenerator::class);

        $this->subscriber = new DefinePasswordSubscriber(
            $this->tokenGeneratorProphecy->reveal()
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
     * plainPassword and password are null.
     */
    public function testPrePersistPlainPasswordAndPasswordAreNull()
    {
        $token        = 'token';
        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->getPlainPassword()->shouldBeCalled()->willReturn(null);
        $userProphecy->getPassword()->shouldBeCalled()->willReturn(null);
        $userProphecy->setPlainPassword($token)->shouldBeCalled();

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($userProphecy->reveal());
        // since plainPassword isn't set, token is generated
        $this->tokenGeneratorProphecy->generateToken()->shouldBeCalled()->willReturn($token);

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }

    /**
     * Test prePersist
     * plainPassword is set.
     */
    public function testPrePersistPlainPasswordIsSet()
    {
        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->getPlainPassword()->shouldBeCalled()->willReturn('plainPassword');
        $userProphecy->setPlainPassword(Argument::any())->shouldNotBeCalled();

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($userProphecy->reveal());
        // since plainPassword is set, no token is generated
        $this->tokenGeneratorProphecy->generateToken()->shouldNotBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }

    /**
     * Test prePersist
     * plainPassword is not set but password is set.
     */
    public function testPrePersistPlainPasswordIsNotSetButPasswordIsSet()
    {
        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->getPlainPassword()->shouldBeCalled()->willReturn(null);
        $userProphecy->getPassword()->shouldBeCalled()->willReturn('plainPassword');
        $userProphecy->setPlainPassword(Argument::any())->shouldNotBeCalled();

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($userProphecy->reveal());
        // since password is set, no token is generated
        $this->tokenGeneratorProphecy->generateToken()->shouldNotBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }
}
