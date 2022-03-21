<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\LateRequestEvent;
use App\Domain\Notification\Event\LateSurveyEvent;
use App\Domain\Notification\Event\NoLoginEvent;
use App\Domain\Notification\Model\Notification;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Notification\Repository\Notification as NotificationRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * This event subscriber creates notification for things that are trigerred by a cron job.
 */
class NotificationEventSubscriber implements EventSubscriberInterface
{
    protected NotificationRepository $notificationRepository;
    protected NormalizerInterface $normalizer;
    protected UserRepository $userRepository;

    public function __construct(
        NotificationRepository $notificationRepository,
        NormalizerInterface $normalizer,
        UserRepository $userRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->normalizer             = $normalizer;
        $this->userRepository         = $userRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            LateActionEvent::class  => 'onLateAction',
            LateRequestEvent::class => 'onLateRequest',
            NoLoginEvent::class     => 'onNoLogin',
            LateSurveyEvent::class  => 'onLateSurvey',
        ];
    }

    public function onLateSurvey(LateSurveyEvent $event)
    {
        $survey       = $event->getSurvey();
        $existing     = $this->notificationRepository->findBy([
            'module'       => 'notification.modules.maturity',
            'collectivity' => $survey->getCollectivity(),
            'action'       => 'notifications.actions.late_survey',
            'name'         => $survey->__toString(),
        ]);
        if (count($existing)) {
            return;
        }
        $norm = $this->normalizer->normalize($survey, null, array_merge(
            [
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH                                 => true,
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT                         => 1,
                AbstractObjectNormalizer::MAX_DEPTH_HANDLER                                => function ($o) {return $o->getId(); },
            ],
            $this->setMaxDepth($survey)
        ));
        $notification = new Notification();
        $notification->setModule('notification.modules.maturity');
        $notification->setCollectivity($survey->getCollectivity());
        $notification->setAction('notifications.actions.late_survey');
        $notification->setName($survey->__toString());
        $notification->setObject((object) $norm);
        $this->notificationRepository->insert($notification);

        $users = $this->userRepository->findNonDpoUsers();
        foreach ($users as $user) {
            $notification = new Notification();
            $notification->setModule('notification.modules.maturity');
            $notification->setCollectivity($survey->getCollectivity());
            $notification->setAction('notifications.actions.late_survey');
            $notification->setName($survey->__toString());
            $notification->setUser($user);
            $notification->setObject((object) $norm);
            $this->notificationRepository->insert($notification);
        }

        // TODO Send email to référent opérationnel
    }

    public function onLateAction(LateActionEvent $event)
    {
        $action       = $event->getMesurement();
        $existing     = $this->notificationRepository->findBy([
            'module'       => 'notification.modules.action',
            'collectivity' => $action->getCollectivity(),
            'action'       => 'notifications.actions.late_action',
            'name'         => $action->getName(),
        ]);
        if (count($existing)) {
            return;
        }

        $norm = $this->normalizer->normalize($action, null, array_merge(
            [
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH                                 => true,
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT                         => 1,
                AbstractObjectNormalizer::MAX_DEPTH_HANDLER                                => function ($o) {return $o->getId(); },
            ],
            $this->setMaxDepth($action)
        ));
        $notification = new Notification();
        $notification->setModule('notification.modules.action');
        $notification->setCollectivity($action->getCollectivity());
        $notification->setAction('notifications.actions.late_action');
        $notification->setName($action->getName());
        $notification->setObject((object) $norm);
        $this->notificationRepository->insert($notification);

        $users = $this->userRepository->findNonDpoUsers();
        foreach ($users as $user) {
            $notification = new Notification();
            $notification->setModule('notification.modules.action');
            $notification->setCollectivity($action->getCollectivity());
            $notification->setAction('notifications.actions.late_action');
            $notification->setName($action->getName());
            $notification->setObject((object) $norm);
            $notification->setUser($user);
            $this->notificationRepository->insert($notification);
        }

        // TODO Send email to référent opérationnel
    }

    public function onLateRequest(LateRequestEvent $event)
    {
        $request      = $event->getRequest();
        $existing     = $this->notificationRepository->findBy([
            'module'       => 'notification.modules.request',
            'collectivity' => $request->getCollectivity(),
            'action'       => 'notifications.actions.late_request',
            'name'         => $request->__toString(),
        ]);
        if (count($existing)) {
            return;
        }

        $norm = $this->normalizer->normalize($request, null, array_merge(
            [
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH                                 => true,
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT                         => 1,
                AbstractObjectNormalizer::MAX_DEPTH_HANDLER                                => function ($o) {return $o->getId(); },
            ],
            $this->setMaxDepth($request)
        ));

        $notification = new Notification();
        $notification->setModule('notification.modules.request');
        $notification->setCollectivity($request->getCollectivity());
        $notification->setAction('notifications.actions.late_request');
        $notification->setName($request->__toString());
        $notification->setObject((object) $norm);
        $this->notificationRepository->insert($notification);

        $users = $this->userRepository->findNonDpoUsers();
        foreach ($users as $user) {
            $notification = new Notification();
            $notification->setModule('notification.modules.request');
            $notification->setCollectivity($request->getCollectivity());
            $notification->setAction('notifications.actions.late_request');
            $notification->setName($request->__toString());
            $notification->setObject((object) $norm);
            $notification->setUser($user);
            $this->notificationRepository->insert($notification);
        }

        // TODO Send email to référent opérationnel and responsable de traitement
    }

    public function onNoLogin(NoLoginEvent $event)
    {
        $user         = $event->getUser();
        $existing     = $this->notificationRepository->findBy([
            'module'       => 'notification.modules.user',
            'collectivity' => $user->getCollectivity(),
            'action'       => 'notifications.actions.no_login',
            'name'         => $user->getFullName(),
        ]);
        if (count($existing)) {
            return;
        }
        $notification = new Notification();

        $notification->setModule('notification.modules.user');
        $notification->setCollectivity($user->getCollectivity());
        $notification->setAction('notifications.actions.no_login');
        $notification->setName($user->getFullName());
        $notification->setObject((object) $this->normalizer->normalize($user, null, array_merge(
            [
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH                                 => true,
                AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT                         => 1,
                AbstractObjectNormalizer::MAX_DEPTH_HANDLER                                => function ($o) {return $o->getId(); },
            ],
            $this->setMaxDepth($user)
        )));
        $this->notificationRepository->insert($notification);
    }

    private function setMaxDepth($object)
    {
        $depths  = [];
        $class   = get_class($object);
        $methods = get_class_methods($class);
        foreach ($methods as $method) {
            if ('get' === substr($method, 0, 3)) {
                $property                                     = lcfirst(substr($method, 3));
                $depths['depth_' . $class . '::' . $property] = 0;
            }
        }

        return $depths;
    }
}
