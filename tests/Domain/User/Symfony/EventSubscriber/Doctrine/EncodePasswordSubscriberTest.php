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

use App\Domain\User\Model;
use App\Domain\User\Symfony\EventSubscriber\Doctrine\EncodePasswordSubscriber;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EncodePasswordSubscriberTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EncodePasswordSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);
        $this->passwordEncoder            = $this->prophesize(UserPasswordEncoderInterface::class);

        $this->subscriber = new EncodePasswordSubscriber(
            $this->passwordEncoder->reveal()
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
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    /**
     * Test prePersist
     * plainPassword is null.
     */
    public function testPrePersistPlainPasswordIsNull()
    {
        $user = new Model\User();

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($user);
        // since plainPassword isn't set, no encoder is called
        $this->passwordEncoder->encodePassword(Argument::any())->shouldNotBeCalled();

        // Before
        $this->assertNull($user->getPassword());
        $this->assertNull($user->getPlainPassword());

        // PrePersist
        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());

        // After
        $this->assertNull($user->getPassword());
        $this->assertNull($user->getPlainPassword());
    }

    /**
     * Test prePersist
     * plainPassword is set.
     */
    public function testPrePersistPlainPasswordIsSet()
    {
        $user = new Model\User();
        $user->setPlainPassword('dummyPassword');

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($user);
        // since plainPassword is set, encoder is called
        $this->passwordEncoder->encodePassword($user, 'dummyPassword')->willReturn('foo')->shouldBeCalled();

        // Before
        $this->assertNull($user->getPassword());
        $this->assertNotNull($user->getPlainPassword());

        // PrePersist
        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());

        // After
        $this->assertNotNull($user->getPassword());
        $this->assertNull($user->getPlainPassword());
    }

    /**
     * Test preUpdate
     * plainPassword is null.
     */
    public function testPreUpdatePlainPasswordIsNull()
    {
        $user = new Model\User();

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($user);
        // since plainPassword isn't set, no encoder is called
        $this->passwordEncoder->encodePassword(Argument::any())->shouldNotBeCalled();

        // Before
        $this->assertNull($user->getPassword());
        $this->assertNull($user->getPlainPassword());

        // PrePersist
        $this->subscriber->preUpdate($this->lifeCycleEventArgsProphecy->reveal());

        // After
        $this->assertNull($user->getPassword());
        $this->assertNull($user->getPlainPassword());
    }

    /**
     * Test preUpdate
     * plainPassword is set.
     */
    public function testPreUpdatePlainPasswordIsSet()
    {
        $user = new Model\User();
        // On update, password is already set
        $user->setPassword('ThisIsAnEncodedPassword');
        $user->setPlainPassword('dummyPassword');

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($user);
        // since plainPassword is set, encoder is called
        $this->passwordEncoder->encodePassword($user, 'dummyPassword')->willReturn('foo')->shouldBeCalled();

        // Before
        $this->assertNotNull($user->getPassword());
        $this->assertNotNull($user->getPlainPassword());

        // PrePersist
        $this->subscriber->preUpdate($this->lifeCycleEventArgsProphecy->reveal());

        // After
        $this->assertNotNull($user->getPassword());
        $this->assertNull($user->getPlainPassword());
    }
}
