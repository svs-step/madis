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
        $norm         = $this->normalizer->normalize($survey, null, $this->normalizerOptions());
        $notification = new Notification();
        $notification->setModule('notification.modules.maturity');
        $notification->setCollectivity($survey->getCollectivity());
        $notification->setAction('notifications.actions.late_survey');
        $notification->setName($survey->__toString());
        $notification->setObject((object) $norm);
        $this->notificationRepository->insert($notification);

        $users = $this->userRepository->findNonDpoUsersForCollectivity($survey->getCollectivity());

        $notification = new Notification();
        $notification->setModule('notification.modules.maturity');
        $notification->setCollectivity($survey->getCollectivity());
        $notification->setAction('notifications.actions.late_survey');
        $notification->setName($survey->__toString());
        $notification->setObject((object) $norm);
        $this->notificationRepository->insert($notification);
        $nus = $this->notificationRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);

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

        $norm         = $this->normalizer->normalize($action, null, $this->normalizerOptions());
        $notification = new Notification();
        $notification->setModule('notification.modules.action');
        $notification->setCollectivity($action->getCollectivity());
        $notification->setAction('notifications.actions.late_action');
        $notification->setName($action->getName());
        $notification->setObject((object) $norm);
        $this->notificationRepository->insert($notification);

        $users        = $this->userRepository->findNonDpoUsersForCollectivity($action->getCollectivity());
        $notification = new Notification();
        $notification->setModule('notification.modules.action');
        $notification->setCollectivity($action->getCollectivity());
        $notification->setAction('notifications.actions.late_action');
        $notification->setName($action->getName());
        $notification->setObject((object) $norm);
        $this->notificationRepository->insert($notification);

        $nus = $this->notificationRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);

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

        $norm = $this->normalizer->normalize($request, null, $this->normalizerOptions());

        $notification = new Notification();
        $notification->setModule('notification.modules.request');
        $notification->setCollectivity($request->getCollectivity());
        $notification->setAction('notifications.actions.late_request');
        $notification->setName($request->__toString());
        $notification->setObject((object) $norm);
        $this->notificationRepository->insert($notification);

        $users = $this->userRepository->findNonDpoUsersForCollectivity($request->getCollectivity());

        $notification = new Notification();
        $notification->setModule('notification.modules.request');
        $notification->setCollectivity($request->getCollectivity());
        $notification->setAction('notifications.actions.late_request');
        $notification->setName($request->__toString());
        $notification->setObject((object) $norm);

        $this->notificationRepository->insert($notification);

        $nus = $this->notificationRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);
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
        $notification->setObject((object) $this->normalizer->normalize($user, null, $this->normalizerOptions()));
        $this->notificationRepository->insert($notification);
    }

    private function getObjectSimpleValue($object)
    {
        if (is_object($object)) {
            if (method_exists($object, 'getId')) {
                return $object->getId();
            } elseif (method_exists($object, '__toString')) {
                return $object->__toString();
            } elseif (method_exists($object, 'format')) {
                return $object->format(DATE_ATOM);
            }

            return '';
        }

        return $object;
    }

    private function normalizerOptions()
    {
        return [
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH                                 => true,
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT                         => 1,
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER                       => function ($o) {
                return $this->getObjectSimpleValue($o);
            },
            AbstractObjectNormalizer::MAX_DEPTH_HANDLER          => function ($o) {
                if (is_iterable($o)) {
                    $d = [];
                    foreach ($o as $item) {
                        $d[] = $this->getObjectSimpleValue($item);
                    }

                    return $d;
                }

                return $this->getObjectSimpleValue($o);
            },
        ];
    }
}
