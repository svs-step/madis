<?php

namespace App\Tests\Domain\Notification\Symfony\EventSubscriber\Doctrine;

use App\Domain\Notification\Serializer\NotificationNormalizer;
use App\Domain\Notification\Symfony\EventSubscriber\Doctrine\NotificationEventSubscriber;
use App\Domain\Registry\Calculator\ConformiteOrganisationConformiteCalculator;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Symfony\EventSubscriber\Doctrine\ConformiteOrganisationSubscriber;
use App\Infrastructure\ORM\Notification\Repository\Notification;
use App\Infrastructure\ORM\Notification\Repository\NotificationUser;
use App\Infrastructure\ORM\User\Repository\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Security;
use Doctrine;
use ReflectionClass;
use Doctrine\ORM\Mapping\ClassMetadata;

class NotificationGenerationSubscriberTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $notificationNormalizer;
    private ObjectProphecy $userRepository;
    private ObjectProphecy $security;

    private NotificationEventSubscriber $subscriber;

    public function setUp(): void
    {
        $this->lifeCycleEventArgs = $this->prophesize(OnFlushEventArgs::class);

        $nr = $this->prophesize(Notification::class);
        $this->notificationNormalizer = $this->prophesize(NotificationNormalizer::class);
        $this->userRepository = $this->prophesize(User::class);
        $nur = $this->prophesize(NotificationUser::class);

        $this->security = $this->prophesize(Security::class);
        $this->subscriber = new NotificationEventSubscriber($nr->reveal(), $this->notificationNormalizer->reveal(), $this->userRepository->reveal(), $nur->reveal(), $this->security->reveal());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(EventSubscriber::class, $this->subscriber);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [
                Events::onFlush,
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }

    public function testOnFlush()
    {
        $conn = \Doctrine\DBAL\DriverManager::getConnection(array('driver' => 'pdo_sqlite', 'memory' => true));
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataDriverImpl(\Doctrine\ORM\Mapping\Driver\AnnotationDriver::create());
        $config->setProxyDir(__DIR__ . '/../Proxies');
        $config->setProxyNamespace('DoctrineExtensions\\NestedSet\\Tests\\Proxies');
        $em = \Doctrine\ORM\EntityManager::create($conn, $config);


        $object = new Treatment();

        $om = $this->prophesize(EntityManagerInterface::class);
        $uow = $this->prophesize(UnitOfWork::class);

        $uow->getScheduledEntityInsertions()->shouldBeCalled()->willReturn([$object]);
        $uow->getScheduledEntityUpdates()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityDeletions()->shouldBeCalled()->willReturn([]);

        $om->getUnitOfWork()->shouldBeCalled()->willReturn($uow);
        $this->security->getUser()->shouldBeCalled()->willReturn (new \App\Domain\User\Model\User());

        $meta = $em->getClassMetadata(\App\Domain\Notification\Model\Notification::class);
        $meta2 = $em->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class);

        $om->getClassMetadata(\App\Domain\Notification\Model\Notification::class)->shouldBeCalled()->willReturn($meta);
        $om->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class)->shouldBeCalled()->willReturn($meta2);

        $this->notificationNormalizer->normalize($object, null, Argument::type('array'))->shouldBeCalled();
        $this->userRepository->findNonDpoUsers()->shouldNotBeCalled();
        $this->userRepository->findNonDpoUsersForCollectivity(Argument::any())->shouldNotBeCalled();
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($om);

        //$uow->computeChangeSet($meta, $notif);
        $this->subscriber->onFlush($this->lifeCycleEventArgs->reveal());

        $om->persist(Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldHaveBeenCalled();
        $om->persist(Argument::type(\App\Domain\Notification\Model\NotificationUser::class))->shouldNotHaveBeenCalled();
        $uow->computeChangeSet($meta, Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldHaveBeenCalled();

    }
}
