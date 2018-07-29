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

use App\Domain\User\Component\TokenGenerator;
use App\Domain\User\Model;
use App\Domain\User\Symfony\EventSubscriber\DefinePasswordSubscriber;
use App\Domain\User\Symfony\EventSubscriber\EncodePasswordSubscriber;
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
     * @var EncodePasswordSubscriber
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
     * plainPassword is null.
     */
    public function testPrePersistPlainPasswordIsNull()
    {
        $token        = 'token';
        $userProphecy = $this->prophesize(Model\User::class);
        $userProphecy->getPlainPassword()->shouldBeCalled()->willReturn(null);
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
}
