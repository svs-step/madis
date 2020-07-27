<?php

namespace App\Tests\Domain\Registry\Symfony\EventSubscriber\Kernel;

use App\Domain\Registry\Calculator\ConformiteOrganisationConformiteCalculator;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Symfony\EventSubscriber\Event\ConformiteOrganisationEvent;
use App\Domain\Registry\Symfony\EventSubscriber\Kernel\ConformiteOrganisationSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConformiteOrganisationSubscriberTest extends TestCase
{
    /**
     * @var ConformiteOrganisationSubscriber
     */
    private $subscriber;

    /**
     * @var ConformiteOrganisationConformiteCalculator|ObjectProphecy
     */
    private $calculator;

    /**
     * @var EntityManagerInterface|ObjectProphecy
     */
    private $entityManager;

    public function setUp()
    {
        $this->calculator    = $this->prophesize(ConformiteOrganisationConformiteCalculator::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);

        $this->subscriber = new ConformiteOrganisationSubscriber($this->calculator->reveal(), $this->entityManager->reveal());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [
                ConformiteOrganisationEvent::class => ['calculConformite'],
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testCalculConformites()
    {
        $evaluation = $this->prophesize(Evaluation::class);
        $event      = $this->prophesize(ConformiteOrganisationEvent::class);
        $event->getEvaluation()->willReturn($evaluation);

        $this->calculator->calculEvaluationConformites($evaluation->reveal())->shouldBeCalled();
        $this->subscriber->calculConformite($event->reveal());
    }
}
