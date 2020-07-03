<?php

declare(strict_types=1);

namespace App\Tests\Domain\Reporting\Symfony\EventSubscriber\Doctrine;

use App\Domain\Registry\Model\Treatment;
use App\Domain\Reporting\Symfony\EventSubscriber\Doctrine\LogJournalDoctrineSubscriber;
use App\Domain\Reporting\Symfony\EventSubscriber\Event\LogJournalEvent;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;

class LogJournalDoctrineSubscriberTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LifecycleEventArgs
     */
    private $lifeCycleEventArgsProphecy;

    /**
     * @var LogJournalDoctrineSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->security                   = $this->prophesize(Security::class);
        $this->eventDispatcher            = $this->prophesize(EventDispatcherInterface::class);
        $this->entityManager              = $this->prophesize(EntityManagerInterface::class);
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);

        $this->subscriber = new LogJournalDoctrineSubscriber(
            $this->security->reveal(),
            $this->eventDispatcher->reveal(),
            $this->entityManager->reveal()
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
                Events::postPersist,
                Events::postUpdate,
                Events::postRemove,
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testPostPersist()
    {
        $user         = $this->prophesize(User::class);
        $collectivity = $this->prophesize(Collectivity::class);
        $treatment    = new Treatment();
        $treatment->setCollectivity($collectivity->reveal());

        $this->security->getUser()->willReturn($user->reveal());

        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$treatment]));

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($treatment);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldBeCalled();

        $this->subscriber->postPersist($this->lifeCycleEventArgsProphecy->reveal());
    }

    public function testPostUpdate()
    {
        $user         = $this->prophesize(User::class);
        $collectivity = $this->prophesize(Collectivity::class);
        $treatment    = new Treatment();
        $treatment->setCollectivity($collectivity->reveal());

        $this->security->getUser()->willReturn($user->reveal());

        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$treatment]));

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($treatment);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldBeCalled();

        $this->subscriber->postUpdate($this->lifeCycleEventArgsProphecy->reveal());
    }

    public function testPostRemove()
    {
        $user         = $this->prophesize(User::class);
        $collectivity = $this->prophesize(Collectivity::class);
        $treatment    = new Treatment();
        $treatment->setCollectivity($collectivity->reveal());

        $this->security->getUser()->willReturn($user->reveal());

        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$treatment]));

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($treatment);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldBeCalled();

        $this->subscriber->postRemove($this->lifeCycleEventArgsProphecy->reveal());
    }

    public function testSupports()
    {
        $treatment = new Treatment();
        $this->security->getUser()->willReturn($this->prophesize(User::class));
        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$treatment]));
        $this->security->getUser()->willReturn(null);
        $this->assertFalse($this->invokeMethod($this->subscriber, 'supports', [$treatment]));
    }

    public function testGetCollectivity()
    {
        $this->assertInstanceOf(Collectivity::class, $this->invokeMethod($this->subscriber, 'getCollectivity', [new Collectivity()]));
        $treatment = $this->prophesize(Treatment::class);
        $treatment->getCollectivity()->shouldBeCalled()->willReturn(new Collectivity());
        $this->assertInstanceOf(Collectivity::class, $this->invokeMethod($this->subscriber, 'getCollectivity', [$treatment->reveal()]));
        $user = $this->prophesize(User::class);
        $user->getCollectivity()->shouldBeCalled()->willReturn(new Collectivity());
        $this->security->getUser()->shouldBeCalled()->willReturn($user->reveal());
        $this->assertInstanceOf(Collectivity::class, $this->invokeMethod($this->subscriber, 'getCollectivity', [new \StdClass()]));
    }

    public function testItReturnNullOnLoginUser()
    {
        $user = $this->prophesize(User::class);
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($user->reveal());
        $uow = $this->createMock(UnitOfWork::class);
        $uow->method('getEntityChangeSet')
            ->willReturn(['lastLogin' => []])
        ;
        $this->entityManager->getUnitOfWork()->shouldBeCalled()->willReturn($uow);
        $this->assertNull($this->invokeMethod($this->subscriber, 'registerLogForUser', [$this->lifeCycleEventArgsProphecy->reveal()]));
    }

    public function testItRegisterLogForUser()
    {
        $user = $this->prophesize(User::class);
        $user->getCollectivity()->shouldBeCalled()->willReturn(new Collectivity());
        $this->security->getUser()->shouldBeCalled()->willReturn(new User());
        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($user->reveal());
        $uow = $this->createMock(UnitOfWork::class);
        $uow->method('getEntityChangeSet')
            ->willReturn(['firstName' => [], 'lastName' => [], 'email' => [], 'password' => []])
        ;
        $this->entityManager->getUnitOfWork()->shouldBeCalled()->willReturn($uow);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldBeCalledTimes(4);
        $this->invokeMethod($this->subscriber, 'registerLogForUser', [$this->lifeCycleEventArgsProphecy->reveal()]);
    }
}
