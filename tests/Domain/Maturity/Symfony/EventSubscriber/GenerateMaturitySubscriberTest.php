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

namespace App\Tests\Domain\Maturity\Symfony\EventSubscriber;

use App\Domain\Maturity\Calculator;
use App\Domain\Maturity\Model;
use App\Domain\Maturity\Symfony\EventSubscriber\GenerateMaturitySubscriber;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;

class DefinePasswordSubscriberTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var Calculator\Maturity;
     */
    private $calculatorProphecy;

    /**
     * @var GenerateMaturitySubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);
        $this->calculatorProphecy         = $this->prophesize(Calculator\Maturity::class);

        $this->subscriber = new GenerateMaturitySubscriber(
            $this->calculatorProphecy->reveal()
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
        $objectProphecy = $this->prophesize(Model\Survey::class);
        $maturityList   = [];
        $globalScore    = 0;

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($objectProphecy->reveal());

        $this->calculatorProphecy->generateMaturityByDomain($objectProphecy->reveal())->shouldBeCalled()->willReturn($maturityList);
        $this->calculatorProphecy->getGlobalScore($maturityList)->shouldBeCalled()->willReturn($globalScore);

        $objectProphecy->setMaturity($maturityList)->shouldBeCalled();
        $objectProphecy->setScore($globalScore)->shouldBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgsProphecy->reveal());
    }

    /**
     * Test preUpdate.
     */
    public function testPreUpdate()
    {
        $objectProphecy = $this->prophesize(Model\Survey::class);
        $maturityList   = [];
        $globalScore    = 0;

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($objectProphecy->reveal());

        $this->calculatorProphecy->generateMaturityByDomain($objectProphecy->reveal())->shouldBeCalled()->willReturn($maturityList);
        $this->calculatorProphecy->getGlobalScore($maturityList)->shouldBeCalled()->willReturn($globalScore);

        $objectProphecy->setMaturity($maturityList)->shouldBeCalled();
        $objectProphecy->setScore($globalScore)->shouldBeCalled();

        $this->subscriber->preUpdate($this->lifeCycleEventArgsProphecy->reveal());
    }
}
