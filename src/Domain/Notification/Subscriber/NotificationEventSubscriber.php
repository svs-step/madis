<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\LateRequestEvent;
use App\Domain\Notification\Event\NoLoginEvent;
use App\Domain\Notification\Model\Notification;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Notification\Repository\Notification as NotificationRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This event subscriber creates notification for things that are trigerred by a cron job.
 */
class NotificationEventSubscriber implements EventSubscriberInterface
{
    protected NotificationRepository $notificationRepository;
    protected SerializerInterface $serializer;
    protected UserRepository $userRepository;
    protected TranslatorInterface $translator;

    public function __construct(
        NotificationRepository $notificationRepository,
        SerializerInterface $serializer,
        UserRepository $userRepository,
        TranslatorInterface $translator
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->serializer             = $serializer;
        $this->userRepository         = $userRepository;
        $this->translator             = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            LateActionEvent::class  => 'onLateAction',
            LateRequestEvent::class => 'onLateRequest',
            NoLoginEvent::class     => 'onNoLogin',
        ];
    }

    public function onLateAction(LateActionEvent $event)
    {
        $action       = $event->getMesurement();
        $notification = new Notification();
        $notification->setModule($this->translator->trans('notification.modules.action'));
        $notification->setCollectivity($action->getCollectivity());
        $notification->setAction('notifications.actions.late_action');
        $notification->setName($action->getName());
        $notification->setObject($this->serializer->normalize($action, 'array', [
            'circular_reference_handler' => function ($o) {return $o->getId(); },
        ]));
        $this->notificationRepository->insert($notification);

        // TODO Send email to référent opérationnel
    }

    public function onLateRequest(LateRequestEvent $event)
    {
        $request      = $event->getRequest();
        $notification = new Notification();
        $notification->setModule($this->translator->trans('notification.modules.request'));
        $notification->setCollectivity($request->getCollectivity());
        $notification->setAction('notifications.actions.late_request');
        $notification->setName($request->__toString());
        $notification->setObject($this->serializer->normalize($request, null, [
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH                 => true,
            'depth_App\Domain\Registry\Model\Request::proofs'          => 1,
            'depth_App\Domain\Registry\Model\Request::applicant'       => 1,
            'depth_App\Domain\Registry\Model\Request::concernedPeople' => 1,
            'depth_App\Domain\Registry\Model\Request::answer'          => 1,
            'depth_App\Domain\Registry\Model\Request::service'         => 1,
            'depth_App\Domain\Registry\Model\Request::mesurements'     => 1,
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT         => 2,
        ]));
        $this->notificationRepository->insert($notification);

        // TODO Send email to référent opérationnel and responsable de traitement
    }

    public function onNoLogin(NoLoginEvent $event)
    {
        $user         = $event->getUser();
        $notification = new Notification();
        $notification->setModule($this->translator->trans('notification.modules.user'));
        $notification->setCollectivity($user->getCollectivity());
        $notification->setAction('notifications.actions.no_login');
        $notification->setName($user->getFullName());
        $notification->setObject($this->serializer->normalize($user, null, [
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH         => true,
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT => 2,
        ]));
        $this->notificationRepository->insert($notification);
    }
}
