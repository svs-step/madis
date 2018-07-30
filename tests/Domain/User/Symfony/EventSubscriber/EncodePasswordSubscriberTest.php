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

namespace App\Tests\Domain\User\Symfony\EventSubscriber;

use App\Domain\User\Model;
use App\Domain\User\Symfony\EventSubscriber\EncodePasswordSubscriber;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class EncodePasswordSubscriberTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactoryProphecy;

    /**
     * @var EncodePasswordSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);
        $this->encoderFactoryProphecy     = $this->prophesize(EncoderFactoryInterface::class);

        $this->subscriber = new EncodePasswordSubscriber(
            $this->encoderFactoryProphecy->reveal()
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
        $this->encoderFactoryProphecy->getEncoder()->shouldNotBeCalled();

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
        $this->encoderFactoryProphecy->getEncoder($user)->shouldBeCalled()->willReturn(new BCryptPasswordEncoder(13));

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
        $this->encoderFactoryProphecy->getEncoder()->shouldNotBeCalled();

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
        $this->encoderFactoryProphecy->getEncoder($user)->shouldBeCalled()->willReturn(new BCryptPasswordEncoder(13));

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
