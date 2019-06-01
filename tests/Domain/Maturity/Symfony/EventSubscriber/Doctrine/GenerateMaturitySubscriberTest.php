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

namespace App\Tests\Domain\Maturity\Symfony\EventSubscriber\Doctrine;

use App\Domain\Maturity\Calculator;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Symfony\EventSubscriber\Doctrine\GenerateMaturitySubscriber;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;

class GenerateMaturitySubscriberTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var Calculator\MaturityHandler;
     */
    private $maturityHandlerProphecy;

    /**
     * @var GenerateMaturitySubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);
        $this->maturityHandlerProphecy    = $this->prophesize(Calculator\MaturityHandler::class);

        $this->subscriber = new GenerateMaturitySubscriber(
            $this->maturityHandlerProphecy->reveal()
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
     * Test prePersist.
     */
    public function testPrePersist()
    {
        $object = new Model\Survey();

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->maturityHandlerProphecy->handle($object)->shouldBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }

    /**
     * Test preUpdate.
     */
    public function testPreUpdate()
    {
        $object = new Model\Survey();

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($object);

        $this->maturityHandlerProphecy->handle($object)->shouldBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }
}
