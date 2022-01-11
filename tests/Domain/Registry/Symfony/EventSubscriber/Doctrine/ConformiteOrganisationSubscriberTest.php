<?php

namespace App\Tests\Domain\Registry\Symfony\EventSubscriber\Doctrine;

use App\Domain\Registry\Calculator\ConformiteOrganisationConformiteCalculator;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Symfony\EventSubscriber\Doctrine\ConformiteOrganisationSubscriber;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ConformiteOrganisationSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var LifecycleEventArgs|ObjectProphecy
     */
    private $lifeCycleEventArgs;

    /**
     * @var ConformiteOrganisationConformiteCalculator|ObjectProphecy
     */
    private $calculator;

    /**
     * @var ConformiteOrganisationSubscriber
     */
    private $subscriber;

    public function setUp(): void
    {
        $this->lifeCycleEventArgs = $this->prophesize(LifecycleEventArgs::class);
        $this->calculator         = $this->prophesize(ConformiteOrganisationConformiteCalculator::class);

        $this->subscriber = new ConformiteOrganisationSubscriber($this->calculator->reveal());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(EventSubscriber::class, $this->subscriber);
    }

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

    public function testPrePersist()
    {
        $object = new Evaluation();

        $this->lifeCycleEventArgs->getObject()->shouldBeCalled()->willReturn($object);

        $this->calculator->calculEvaluationConformites($object)->shouldBeCalled();

        $this->subscriber->prePersist($this->lifeCycleEventArgs->reveal());
    }

    public function testPreUpdate()
    {
        $object = new Evaluation();

        $this->lifeCycleEventArgs->getObject()->shouldBeCalled()->willReturn($object);

        $this->calculator->calculEvaluationConformites($object)->shouldBeCalled();

        $this->subscriber->preUpdate($this->lifeCycleEventArgs->reveal());
    }
}
