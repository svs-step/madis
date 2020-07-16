<?php

declare(strict_types=1);

namespace App\Tests\Domain\Reporting\Symfony\EventSubscriber\Doctrine;

use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteOrganisation\Participant;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Reporting\Symfony\EventSubscriber\Doctrine\LogJournalDoctrineSubscriber;
use App\Domain\Reporting\Symfony\EventSubscriber\Event\LogJournalEvent;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\ComiteIlContact;
use App\Domain\User\Model\User;
use App\Tests\Utils\ReflectionTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;

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
     * @var AdapterInterface
     */
    private $cacheAdapter;

    /**
     * @var RequestStack
     */
    private $requestStack;

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
        $this->cacheAdapter               = $this->prophesize(AdapterInterface::class);
        $this->requestStack               = $this->prophesize(RequestStack::class);
        $this->lifeCycleEventArgsProphecy = $this->prophesize(LifecycleEventArgs::class);

        $this->subscriber = new LogJournalDoctrineSubscriber(
            $this->security->reveal(),
            $this->eventDispatcher->reveal(),
            $this->entityManager->reveal(),
            $this->cacheAdapter->reveal(),
            $this->requestStack->reveal()
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
                Events::preRemove,
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testPostPersistAndRegisterALogIfNotPresentInCache()
    {
        $user         = new User();
        $collectivity = $this->prophesize(Collectivity::class);
        $treatment    = new Treatment();
        $treatment->setCollectivity($collectivity->reveal());
        $cacheItem = $this->prophesize(ItemInterface::class);

        $this->cacheAdapter->getItem(Argument::type('string'))->shouldBeCalled()->willReturn($cacheItem->reveal());
        $cacheItem->isHit()->shouldBeCalled()->willReturn(false);
        $cacheItem->expiresAfter(2)->shouldBeCalled();
        $cacheItem->set($treatment->getId())->shouldBeCalled();
        $this->cacheAdapter->save($cacheItem)->shouldBeCalled();

        $this->security->getUser()->willReturn($user);

        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$treatment]));

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($treatment);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldBeCalled();

        $this->subscriber->postPersist($this->lifeCycleEventArgsProphecy->reveal());
    }

    public function testPostPersistAndNotRegisterALogIfAlreadyPresentInCache()
    {
        $user         = new User();
        $collectivity = $this->prophesize(Collectivity::class);
        $treatment    = new Treatment();
        $treatment->setCollectivity($collectivity->reveal());
        $cacheItem = $this->prophesize(ItemInterface::class);
        $cacheItem->get()->willReturn($treatment->getId());

        $this->security->getUser()->willReturn($user);

        $this->cacheAdapter->getItem(Argument::type('string'))->shouldBeCalled()->willReturn($cacheItem->reveal());
        $cacheItem->isHit()->shouldBeCalled()->willReturn(true);

        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$treatment]));

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($treatment);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldNotBeCalled();

        $this->assertNull($this->subscriber->postPersist($this->lifeCycleEventArgsProphecy->reveal()));
    }

    public function testPostUpdate()
    {
        $user         = new User();
        $collectivity = $this->prophesize(Collectivity::class);
        $treatment    = new Treatment();
        $treatment->setCollectivity($collectivity->reveal());

        $cacheItem = $this->prophesize(ItemInterface::class);

        $this->cacheAdapter->getItem(Argument::type('string'))->shouldBeCalled()->willReturn($cacheItem->reveal());
        $cacheItem->isHit()->shouldBeCalled()->willReturn(false);
        $cacheItem->expiresAfter(2)->shouldBeCalled();
        $cacheItem->set($treatment->getId())->shouldBeCalled();
        $this->cacheAdapter->save($cacheItem)->shouldBeCalled();

        $this->security->getUser()->willReturn($user);

        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$treatment]));

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($treatment);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldBeCalled();

        $this->subscriber->postUpdate($this->lifeCycleEventArgsProphecy->reveal());
    }

    public function testPreRemove()
    {
        $user         = $this->prophesize(User::class);
        $collectivity = $this->prophesize(Collectivity::class);
        $treatment    = new Treatment();
        $treatment->setCollectivity($collectivity->reveal());

        $this->security->getUser()->willReturn($user->reveal());

        $this->assertTrue($this->invokeMethod($this->subscriber, 'supports', [$treatment]));

        $this->lifeCycleEventArgsProphecy->getObject()->shouldBeCalled()->willReturn($treatment);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldBeCalled();

        $this->subscriber->preRemove($this->lifeCycleEventArgsProphecy->reveal());
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
        $uow  = $this->createMock(UnitOfWork::class);
        $uow->method('getEntityChangeSet')
            ->willReturn(['lastLogin' => []])
        ;
        $this->entityManager->getUnitOfWork()->shouldBeCalled()->willReturn($uow);
        $this->assertNull($this->invokeMethod($this->subscriber, 'registerLogForUser', [$user->reveal()]));
    }

    public function testItRegisterLogForUser()
    {
        $user = $this->prophesize(User::class);
        $user->getCollectivity()->shouldBeCalled()->willReturn(new Collectivity());
        $this->security->getUser()->shouldBeCalled()->willReturn(new User());
        $uow = $this->createMock(UnitOfWork::class);
        $uow->method('getEntityChangeSet')
            ->willReturn(['firstName' => [], 'lastName' => [], 'email' => [], 'password' => []])
        ;
        $this->entityManager->getUnitOfWork()->shouldBeCalled()->willReturn($uow);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldBeCalledTimes(4);
        $this->invokeMethod($this->subscriber, 'registerLogForUser', [$user->reveal()]);
    }

    public function testItRegisterLogForSoftDeleteUser()
    {
        $user = $this->prophesize(User::class);
        $user->getCollectivity()->shouldBeCalled()->willReturn(new Collectivity());
        $this->security->getUser()->shouldBeCalled()->willReturn(new User());
        $uow = $this->createMock(UnitOfWork::class);
        $uow->method('getEntityChangeSet')
            ->willReturn(['deletedAt' => [null, new \DateTimeImmutable()]])
        ;
        $this->entityManager->getUnitOfWork()->shouldBeCalled()->willReturn($uow);
        $this->eventDispatcher->dispatch(Argument::type(LogJournalEvent::class))->shouldBeCalled();
        $this->invokeMethod($this->subscriber, 'registerLogForUser', [$user->reveal()]);
    }

    /**
     * @dataProvider NotConcernedByDeletionLog
     */
    public function testNotConcernedByDeletionLog(string $className)
    {
        $this->assertTrue($this->invokeMethod($this->subscriber, 'notConcernedByDeletionLog', [new $className()]));
    }

    public function NotConcernedByDeletionLog()
    {
        return [
            [Conformite::class],
            [Participant::class],
            [ComiteIlContact::class],
            [Reponse::class],
        ];
    }
}
