<?php

namespace App\Tests\Domain\Notification\Symfony\EventSubscriber\Doctrine;

use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\Notification\Serializer\NotificationNormalizer;
use App\Domain\Notification\Symfony\EventSubscriber\Doctrine\NotificationEventSubscriber;
use App\Domain\Registry\Dictionary\ProofTypeDictionary;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Treatment;
use App\Domain\User\Model\Collectivity;
use App\Infrastructure\ORM\Notification\Repository\Notification;
use App\Infrastructure\ORM\Notification\Repository\NotificationUser;
use App\Infrastructure\ORM\User\Repository\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationGenerationSubscriberTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $notificationNormalizer;
    private ObjectProphecy $userRepository;
    private ObjectProphecy $security;
    private ObjectProphecy $notificationUserRepository;
    private ObjectProphecy $translator;

    private NotificationEventSubscriber $subscriber;

    private $notificationMetadata;
    private $notificationUserMetadata;

    public function setUp(): void
    {
        $this->lifeCycleEventArgs = $this->prophesize(OnFlushEventArgs::class);

        $nr                               = $this->prophesize(Notification::class);
        $this->notificationNormalizer     = $this->prophesize(NotificationNormalizer::class);
        $this->userRepository             = $this->prophesize(User::class);
        $this->notificationUserRepository = $this->prophesize(NotificationUser::class);
        $this->translator                 = $this->prophesize(TranslatorInterface::class);

        $this->security   = $this->prophesize(Security::class);
        $this->subscriber = new NotificationEventSubscriber(
            $nr->reveal(),
            $this->notificationNormalizer->reveal(),
            $this->userRepository->reveal(),
            $this->notificationUserRepository->reveal(),
            $this->security->reveal(),
            $this->translator->reveal(),
            30,
            30
        );

        $conn   = \Doctrine\DBAL\DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataDriverImpl(\Doctrine\ORM\Mapping\Driver\AnnotationDriver::create());
        $config->setProxyDir(__DIR__ . '/../Proxies');
        $config->setProxyNamespace('DoctrineExtensions\\NestedSet\\Tests\\Proxies');
        $em = \Doctrine\ORM\EntityManager::create($conn, $config);

        $this->notificationMetadata     = $em->getClassMetadata(\App\Domain\Notification\Model\Notification::class);
        $this->notificationUserMetadata = $em->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class);
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

    public function testCreateTreatmentNotification()
    {
        $object = new Treatment();

        $om  = $this->prophesize(EntityManagerInterface::class);
        $uow = $this->prophesize(UnitOfWork::class);

        $uow->getScheduledEntityInsertions()->shouldBeCalled()->willReturn([$object]);
        $uow->getScheduledEntityUpdates()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityDeletions()->shouldBeCalled()->willReturn([]);

        $om->getUnitOfWork()->shouldBeCalled()->willReturn($uow);
        $this->security->getUser()->shouldBeCalled()->willReturn(new \App\Domain\User\Model\User());

        $om->getClassMetadata(\App\Domain\Notification\Model\Notification::class)->shouldBeCalled()->willReturn($this->notificationMetadata);
        $om->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class)->shouldBeCalled()->willReturn($this->notificationUserMetadata);

        $this->notificationNormalizer->normalize($object, null, Argument::type('array'))->shouldBeCalled();
        $this->userRepository->findNonDpoUsers()->shouldNotBeCalled();
        $this->userRepository->findNonDpoUsersForCollectivity(Argument::any())->shouldNotBeCalled();
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($om);

        // $uow->computeChangeSet($meta, $notif);
        $this->subscriber->onFlush($this->lifeCycleEventArgs->reveal());

        $notification = new \App\Domain\Notification\Model\Notification();
        $notification->setModule('notification.modules.treatment');
        $notification->setCollectivity($object->getCollectivity());
        $notification->setName($object->getName());
        $notification->setAction('notification.actions.create');
        $notification->setCreatedBy(null);
        $notification->setObject((object) []);

        $om->persist(new NotificationToken($notification))->shouldHaveBeenCalled();
        $om->persist(Argument::type(\App\Domain\Notification\Model\NotificationUser::class))->shouldNotHaveBeenCalled();
        $uow->computeChangeSet($this->notificationMetadata, Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldHaveBeenCalled();
    }

    public function testUpdateTreatmentNotification()
    {
        $object = new Treatment();

        $om  = $this->prophesize(EntityManagerInterface::class);
        $uow = $this->prophesize(UnitOfWork::class);

        $uow->getScheduledEntityInsertions()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityUpdates()->shouldBeCalled()->willReturn([$object]);
        $uow->getScheduledEntityDeletions()->shouldBeCalled()->willReturn([]);

        $om->getUnitOfWork()->shouldBeCalled()->willReturn($uow);

        $this->security->getUser()->shouldBeCalled()->willReturn(new \App\Domain\User\Model\User());

        $om->getClassMetadata(\App\Domain\Notification\Model\Notification::class)->shouldBeCalled()->willReturn($this->notificationMetadata);
        $om->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class)->shouldBeCalled()->willReturn($this->notificationUserMetadata);

        $this->notificationNormalizer->normalize($object, null, Argument::type('array'))->shouldBeCalled();
        $this->userRepository->findNonDpoUsers()->shouldNotBeCalled();
        $this->userRepository->findNonDpoUsersForCollectivity(Argument::any())->shouldNotBeCalled();
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($om);

        $this->subscriber->onFlush($this->lifeCycleEventArgs->reveal());

        $notification = new \App\Domain\Notification\Model\Notification();
        $notification->setModule('notification.modules.treatment');
        $notification->setCollectivity($object->getCollectivity());
        $notification->setName($object->getName());
        $notification->setAction('notification.actions.update');
        $notification->setCreatedBy(null);
        $notification->setObject((object) []);

        $om->persist(new NotificationToken($notification))->shouldHaveBeenCalled();
        $om->persist(Argument::type(\App\Domain\Notification\Model\NotificationUser::class))->shouldNotHaveBeenCalled();
        $uow->computeChangeSet($this->notificationMetadata, Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldHaveBeenCalled();
    }

    public function testDeleteTreatmentNotification()
    {
        $object = new Treatment();

        $om  = $this->prophesize(EntityManagerInterface::class);
        $uow = $this->prophesize(UnitOfWork::class);

        $uow->getScheduledEntityInsertions()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityUpdates()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityDeletions()->shouldBeCalled()->willReturn([$object]);

        $om->getUnitOfWork()->shouldBeCalled()->willReturn($uow);

        $this->security->getUser()->shouldBeCalled()->willReturn(new \App\Domain\User\Model\User());

        $om->getClassMetadata(\App\Domain\Notification\Model\Notification::class)->shouldBeCalled()->willReturn($this->notificationMetadata);
        $om->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class)->shouldBeCalled()->willReturn($this->notificationUserMetadata);

        $this->notificationNormalizer->normalize($object, null, Argument::type('array'))->shouldBeCalled();
        $this->userRepository->findNonDpoUsers()->shouldNotBeCalled();
        $this->userRepository->findNonDpoUsersForCollectivity(Argument::any())->shouldNotBeCalled();
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($om);

        $this->subscriber->onFlush($this->lifeCycleEventArgs->reveal());

        $notification = new \App\Domain\Notification\Model\Notification();
        $notification->setModule('notification.modules.treatment');
        $notification->setCollectivity($object->getCollectivity());
        $notification->setName($object->getName());
        $notification->setAction('notification.actions.delete');
        $notification->setCreatedBy(null);
        $notification->setObject((object) []);

        $om->persist(new NotificationToken($notification))->shouldHaveBeenCalled();

        $om->persist(Argument::type(\App\Domain\Notification\Model\NotificationUser::class))->shouldNotHaveBeenCalled();
        $uow->computeChangeSet($this->notificationMetadata, Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldHaveBeenCalled();
    }

    public function testCreateProofNotification()
    {
        $object = new Proof();
        $object->setType(ProofTypeDictionary::TYPE_POLICY_MANAGEMENT);

        $om  = $this->prophesize(EntityManagerInterface::class);
        $uow = $this->prophesize(UnitOfWork::class);

        $uow->getScheduledEntityInsertions()->shouldBeCalled()->willReturn([$object]);
        $uow->getScheduledEntityUpdates()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityDeletions()->shouldBeCalled()->willReturn([]);

        $om->getUnitOfWork()->shouldBeCalled()->willReturn($uow);
        $this->security->getUser()->shouldBeCalled()->willReturn(new \App\Domain\User\Model\User());

        $om->getClassMetadata(\App\Domain\Notification\Model\Notification::class)->shouldBeCalled()->willReturn($this->notificationMetadata);
        $om->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class)->shouldBeCalled()->willReturn($this->notificationUserMetadata);

        $this->notificationNormalizer->normalize($object, null, Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn((object) [
                'type' => ProofTypeDictionary::TYPE_POLICY_MANAGEMENT,
            ]);
        $this->userRepository->findNonDpoUsers()->shouldNotBeCalled();
        $this->userRepository->findNonDpoUsersForCollectivity(Argument::any())->shouldNotBeCalled();
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($om);

        // $uow->computeChangeSet($meta, $notif);
        $this->subscriber->onFlush($this->lifeCycleEventArgs->reveal());

        $notification = new \App\Domain\Notification\Model\Notification();
        $notification->setModule('notification.modules.proof');
        $notification->setCollectivity($object->getCollectivity());
        $notification->setName($object->getName());
        $notification->setAction('notification.actions.create');
        $notification->setCreatedBy(null);
        $notification->setObject((object) ['type' => ProofTypeDictionary::TYPE_POLICY_MANAGEMENT]);

        $om->persist(new NotificationToken($notification))->shouldHaveBeenCalled();
        $om->persist(Argument::type(\App\Domain\Notification\Model\NotificationUser::class))->shouldNotHaveBeenCalled();
        $uow->computeChangeSet($this->notificationMetadata, Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldHaveBeenCalled();
    }

    public function testUpdateProofNotification()
    {
        $object = new Proof();
        $object->setType(ProofTypeDictionary::TYPE_POLICY_MANAGEMENT);

        $om  = $this->prophesize(EntityManagerInterface::class);
        $uow = $this->prophesize(UnitOfWork::class);

        $uow->getScheduledEntityInsertions()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityUpdates()->shouldBeCalled()->willReturn([$object]);
        $uow->getScheduledEntityDeletions()->shouldBeCalled()->willReturn([]);

        $om->getUnitOfWork()->shouldBeCalled()->willReturn($uow);

        $this->security->getUser()->shouldBeCalled()->willReturn(new \App\Domain\User\Model\User());

        $om->getClassMetadata(\App\Domain\Notification\Model\Notification::class)->shouldBeCalled()->willReturn($this->notificationMetadata);
        $om->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class)->shouldBeCalled()->willReturn($this->notificationUserMetadata);

        $this->notificationNormalizer->normalize($object, null, Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn((object) [
                'type' => ProofTypeDictionary::TYPE_POLICY_MANAGEMENT,
            ]);
        $this->userRepository->findNonDpoUsers()->shouldNotBeCalled();
        $this->userRepository->findNonDpoUsersForCollectivity(Argument::any())->shouldNotBeCalled();
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($om);

        $this->subscriber->onFlush($this->lifeCycleEventArgs->reveal());

        $notification = new \App\Domain\Notification\Model\Notification();
        $notification->setModule('notification.modules.proof');
        $notification->setCollectivity($object->getCollectivity());
        $notification->setName($object->getName());
        $notification->setAction('notification.actions.update');
        $notification->setCreatedBy(null);
        $notification->setObject((object) ['type' => ProofTypeDictionary::TYPE_POLICY_MANAGEMENT]);

        $om->persist(new NotificationToken($notification))->shouldHaveBeenCalled();
        $om->persist(Argument::type(\App\Domain\Notification\Model\NotificationUser::class))->shouldNotHaveBeenCalled();
        $uow->computeChangeSet($this->notificationMetadata, Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldHaveBeenCalled();
    }

    public function testDeleteProofNotification()
    {
        $object = new Proof();
        $object->setType(ProofTypeDictionary::TYPE_POLICY_MANAGEMENT);

        $om  = $this->prophesize(EntityManagerInterface::class);
        $uow = $this->prophesize(UnitOfWork::class);

        $uow->getScheduledEntityInsertions()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityUpdates()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityDeletions()->shouldBeCalled()->willReturn([$object]);

        $om->getUnitOfWork()->shouldBeCalled()->willReturn($uow);

        $this->security->getUser()->shouldBeCalled()->willReturn(new \App\Domain\User\Model\User());

        $om->getClassMetadata(\App\Domain\Notification\Model\Notification::class)
            ->shouldBeCalled()
            ->willReturn($this->notificationMetadata);
        $om->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class)
            ->shouldBeCalled()
            ->willReturn($this->notificationUserMetadata);

        $this->notificationNormalizer->normalize($object, null, Argument::type('array'))
            ->shouldBeCalled()->willReturn((object) [
                'type' => ProofTypeDictionary::TYPE_POLICY_MANAGEMENT,
            ]);
        $this->userRepository->findNonDpoUsers()->shouldNotBeCalled();
        $this->userRepository->findNonDpoUsersForCollectivity(Argument::any())->shouldNotBeCalled();
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($om);

        $this->subscriber->onFlush($this->lifeCycleEventArgs->reveal());

        $notification = new \App\Domain\Notification\Model\Notification();
        $notification->setModule('notification.modules.proof');
        $notification->setCollectivity($object->getCollectivity());
        $notification->setName($object->getName());
        $notification->setAction('notification.actions.delete');
        $notification->setCreatedBy(null);
        $notification->setObject((object) ['type' => ProofTypeDictionary::TYPE_POLICY_MANAGEMENT]);

        $om->persist(new NotificationToken($notification))->shouldHaveBeenCalled();

        $om->persist(Argument::type(\App\Domain\Notification\Model\NotificationUser::class))->shouldNotHaveBeenCalled();
        $uow->computeChangeSet($this->notificationMetadata, Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldHaveBeenCalled();
    }

    public function testDeleteAnalyseImpactNotification()
    {
        $collectivity = new Collectivity();
        $collectivity->setName('coll');
        $treatment = new Treatment();
        $treatment->setName('traitement 1');
        $treatment->setCollectivity($collectivity);
        $conform = new ConformiteTraitement();
        $conform->setTraitement($treatment);
        $object = new AnalyseImpact();
        $object->setConformiteTraitement($conform);
        $object->setCreatedAt(new \DateTimeImmutable());

        $registry = $this->prophesize(ManagerRegistry::class);

        $om  = $this->prophesize(EntityManagerInterface::class);
        $uow = $this->prophesize(UnitOfWork::class);

        $uow->getScheduledEntityInsertions()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityUpdates()->shouldBeCalled()->willReturn([]);
        $uow->getScheduledEntityDeletions()->shouldBeCalled()->willReturn([$object]);

        $om->getUnitOfWork()->shouldBeCalled()->willReturn($uow);
        $this->security->getUser()->shouldBeCalled()->willReturn(new \App\Domain\User\Model\User());

        $om->getClassMetadata(\App\Domain\Notification\Model\Notification::class)->shouldNotBeCalled();
        $om->getClassMetadata(\App\Domain\Notification\Model\NotificationUser::class)->shouldNotBeCalled();

        $this->notificationNormalizer->normalize($object, null, Argument::type('array'))->shouldNotBeCalled();

        $nonDpoUser = new \App\Domain\User\Model\User();
        $nonDpoUser->setCollectivity($collectivity);
        $nonDpoUser->setFirstName('jon');

        $this->userRepository->findNonDpoUsers()->shouldNotBeCalled();
        $this->userRepository->findNonDpoUsersForCollectivity($collectivity)->shouldNotBeCalled();
        $this->lifeCycleEventArgs->getObjectManager()->shouldBeCalled()->willReturn($om);

        $this->notificationUserRepository->saveUsers(Argument::type(\App\Domain\Notification\Model\Notification::class), Argument::cetera())
            ->shouldNotBeCalled()
        ;

        $this->subscriber->onFlush($this->lifeCycleEventArgs->reveal());

        $om->persist(Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldNotHaveBeenCalled();
        $om->persist(Argument::type(\App\Domain\Notification\Model\NotificationUser::class))->shouldNotHaveBeenCalled();
        $uow->computeChangeSet($this->notificationMetadata, Argument::type(\App\Domain\Notification\Model\Notification::class))->shouldNotHaveBeenCalled();
        $uow->computeChangeSet($this->notificationUserMetadata, Argument::type(\App\Domain\Notification\Model\NotificationUser::class))->shouldNotHaveBeenCalled();
    }
}
