<?php

namespace App\Tests\Domain\Notification\Subscriber;

use App\Domain\Maturity\Model\Survey;
use App\Domain\Notification\Dictionary\NotificationModuleDictionary;
use App\Domain\Notification\Event\ConformiteTraitementNeedsAIPDEvent;
use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\LateRequestEvent;
use App\Domain\Notification\Event\LateSurveyEvent;
use App\Domain\Notification\Event\NoLoginEvent;
use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\NotificationUser;
use App\Domain\Notification\Serializer\NotificationNormalizer;
use App\Domain\Notification\Subscriber\NotificationEventSubscriber;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Request;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Notification\Repository\Notification as NotificationRepository;
use App\Infrastructure\ORM\Notification\Repository\NotificationUser as NotificationUserRepository;
use App\Tests\Domain\Notification\Symfony\EventSubscriber\Doctrine\NotificationToken;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationEventSubscriberTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $notificationNormalizer;
    private ObjectProphecy $userRepository;
    private ObjectProphecy $notificationUserRepository;
    private ObjectProphecy $notificationRepository;
    private ObjectProphecy $translator;

    private NotificationEventSubscriber $subscriber;

    public function setUp(): void
    {
        $this->notificationRepository     = $this->prophesize(NotificationRepository::class);
        $this->notificationNormalizer     = $this->prophesize(NotificationNormalizer::class);
        $this->userRepository             = $this->prophesize(UserRepository::class);
        $this->notificationUserRepository = $this->prophesize(NotificationUserRepository::class);
        $this->translator                 = $this->prophesize(TranslatorInterface::class);

        $this->subscriber = new NotificationEventSubscriber(
            $this->notificationRepository->reveal(),
            $this->notificationUserRepository->reveal(),
            $this->notificationNormalizer->reveal(),
            $this->userRepository->reveal(),
            $this->translator->reveal(),
            30,
            30
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->subscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertEquals([
            LateActionEvent::class                    => 'onLateAction',
            LateRequestEvent::class                   => 'onLateRequest',
            NoLoginEvent::class                       => 'onNoLogin',
            LateSurveyEvent::class                    => 'onLateSurvey',
            ConformiteTraitementNeedsAIPDEvent::class => 'onNeedsAIPD',
        ], NotificationEventSubscriber::getSubscribedEvents());
    }

    public function testLateSurveyEvent()
    {
        $collectivity = new Collectivity();
        $collectivity->setName('coll');
        $survey = new Survey();
        $survey->setCollectivity($collectivity);
        $survey->setCreatedAt(new \DateTimeImmutable());

        $this->notificationRepository->findBy([
            'module'       => 'notification.modules.maturity',
            'collectivity' => $survey->getCollectivity(),
            'action'       => 'notifications.actions.late_survey',
            'name'         => $survey->__toString(),
        ])->shouldBeCalled()->willReturn([]);

        $user = new User();

        $notification = new Notification();

        $notification->setModule('notification.modules.maturity');
        $notification->setCollectivity($survey->getCollectivity());
        $notification->setAction('notifications.actions.late_survey');
        $notification->setName($survey->__toString());
        $notification->setDpo(true);
        $notification->setObject((object) [
            'collectivity' => [
                'name' => 'coll',
            ],
        ]);

        $this->notificationRepository->insert(new NotificationToken($notification))->shouldBeCalled();

        $nu = new NotificationUser();
        $nu->setUser($user);
        $nu->setNotification($notification);

        $notification->setNotificationUsers([$user]);

        $this->notificationRepository->update(new NotificationToken($notification))->shouldBeCalled();

        $this->userRepository->findNonDpoUsersForCollectivity($collectivity)->shouldBeCalled()->willReturn([$user]);

        $this->notificationUserRepository->saveUsers(new NotificationToken($notification), [$user])->shouldBeCalled()->willReturn([$nu]);

        $this->notificationNormalizer->normalize(Argument::exact($survey), null, Argument::any())
            ->shouldBeCalled()
            ->willReturn((object) [
                'collectivity' => [
                    'name' => 'coll',
                ],
            ])
        ;

        $event = new LateSurveyEvent($survey);

        $this->subscriber->onLateSurvey($event);
    }

    public function testLateRequestEvent()
    {
        $collectivity = new Collectivity();
        $collectivity->setName('coll');
        $request = new Request();
        $request->setCollectivity($collectivity);
        $request->setCreatedAt(new \DateTimeImmutable());

        $this->notificationRepository->findBy([
            'module'       => 'notification.modules.request',
            'collectivity' => $request->getCollectivity(),
            'action'       => 'notifications.actions.late_request',
            'name'         => $request->__toString(),
        ])->shouldBeCalled()->willReturn([]);

        $user = new User();

        $notification = new Notification();

        $notification->setModule('notification.modules.request');
        $notification->setCollectivity($request->getCollectivity());
        $notification->setAction('notifications.actions.late_request');
        $notification->setName($request->__toString());
        $notification->setDpo(true);
        $notification->setObject((object) [
            'collectivity' => [
                'name' => 'coll',
            ],
        ]);

        $this->notificationRepository->insert(new NotificationToken($notification))->shouldBeCalled();

        $nu = new NotificationUser();
        $nu->setUser($user);
        $nu->setNotification($notification);

        $notification->setNotificationUsers([$user]);

        $this->notificationRepository->update(new NotificationToken($notification))->shouldBeCalled();

        $this->userRepository->findNonDpoUsersForCollectivity($collectivity)->shouldBeCalled()->willReturn([$user]);

        $this->notificationUserRepository->saveUsers(new NotificationToken($notification), [$user])->shouldBeCalled()->willReturn([$nu]);

        $this->notificationNormalizer->normalize(Argument::exact($request), null, Argument::any())
            ->shouldBeCalled()
            ->willReturn((object) [
                'collectivity' => [
                    'name' => 'coll',
                ],
            ])
        ;

        // TODO add check that emails are sent to both ref OP and resp trait.

        $event = new LateRequestEvent($request);

        $this->subscriber->onLateRequest($event);
    }

    public function testLateActionEvent()
    {
        $collectivity = new Collectivity();
        $collectivity->setName('coll');
        $action = new Mesurement();
        $action->setCollectivity($collectivity);
        $action->setCreatedAt(new \DateTimeImmutable());
        // $action->setPlanificationDate((new \DateTime())->sub(new \DateInterval('P3M')));

        $this->notificationRepository->findBy([
            'module'       => 'notification.modules.' . NotificationModuleDictionary::ACTION_PLAN,
            'collectivity' => $action->getCollectivity(),
            'action'       => 'notifications.actions.late_action',
            'name'         => $action->__toString(),
        ])->shouldBeCalled()->willReturn([]);

        $user = new User();
        $user->setCollectivity($collectivity);

        $notification = new Notification();

        $notification->setModule('notification.modules.' . NotificationModuleDictionary::ACTION_PLAN);
        $notification->setCollectivity($action->getCollectivity());
        $notification->setAction('notifications.actions.late_action');
        $notification->setName($action->__toString());
        $notification->setDpo(true);
        $notification->setObject((object) [
            'collectivity' => [
                'name' => 'coll',
            ],
            'planificationDate' => (new \DateTime())->sub(new \DateInterval('P3M'))->format(DATE_ATOM),
        ]);

        $this->notificationRepository->insert(Argument::any())->shouldBeCalled();

        $nu = new NotificationUser();
        $nu->setUser($user);
        $nu->setNotification($notification);
        $notification->setNotificationUsers([$nu]);

        $this->notificationRepository->update(Argument::any())->shouldBeCalled();
        $this->userRepository->findNonDpoUsersForCollectivity($collectivity)->shouldBeCalled()->willReturn([$user]);
        $this->notificationUserRepository->saveUsers(Argument::any(), Argument::any())
            ->shouldBeCalled()
            ->willReturn([$nu]);

        $this->notificationNormalizer->normalize(Argument::exact($action), null, Argument::any())
            ->shouldBeCalled()
            ->willReturn((object) [
                'collectivity' => [
                    'name' => 'coll',
                ],
                'planificationDate' => (new \DateTime())->sub(new \DateInterval('P3M'))->format(DATE_ATOM),
            ])
        ;

        $event = new LateActionEvent($action);

        $this->subscriber->onLateAction($event);
    }
}
