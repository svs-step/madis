<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\LateRequestEvent;
use App\Domain\Notification\Event\LateSurveyEvent;
use App\Domain\Notification\Event\NoLoginEvent;
use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\NotificationUser;
use App\Domain\Notification\Serializer\NotificationNormalizer;
use App\Domain\User\Dictionary\UserMoreInfoDictionary;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Notification\Repository\Notification as NotificationRepository;
use App\Infrastructure\ORM\Notification\Repository\NotificationUser as NotificationUserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/**
 * This event subscriber creates notification for things that are trigerred by a cron job.
 */
class NotificationEventSubscriber implements EventSubscriberInterface
{
    protected NotificationRepository $notificationRepository;
    protected NotificationUserRepository $notificationUserRepository;
    protected NotificationNormalizer $normalizer;
    protected UserRepository $userRepository;

    public function __construct(
        NotificationRepository $notificationRepository,
        NotificationUserRepository $notificationUserRepository,
        NotificationNormalizer $normalizer,
        UserRepository $userRepository
    ) {
        $this->notificationRepository     = $notificationRepository;
        $this->notificationUserRepository = $notificationUserRepository;
        $this->normalizer                 = $normalizer;
        $this->userRepository             = $userRepository;
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
        $survey   = $event->getSurvey();
        $existing = $this->notificationRepository->findBy([
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
        $nus = $this->notificationUserRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);

        // TODO Send email to référent opérationnel
    }

    public function onLateAction(LateActionEvent $event)
    {
        $action   = $event->getMesurement();
        $existing = $this->notificationRepository->findBy([
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

        $nus = $this->notificationUserRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);

        // TODO Send email to référent opérationnel
    }

    public function onLateRequest(LateRequestEvent $event)
    {
        $request  = $event->getRequest();
        $existing = $this->notificationRepository->findBy([
            'module'       => 'notification.modules.request',
            'collectivity' => $request->getCollectivity(),
            'action'       => 'notifications.actions.late_request',
            'name'         => $request->__toString(),
        ]);
        if (count($existing)) {
            return;
        }

        $norm = $this->normalizer->normalize($request, null, $this->normalizerOptions());

        $users = $this->userRepository->findNonDpoUsersForCollectivity($request->getCollectivity());

        $notification = new Notification();
        $notification->setModule('notification.modules.request');
        $notification->setCollectivity($request->getCollectivity());
        $notification->setAction('notifications.actions.late_request');
        $notification->setName($request->__toString());
        $notification->setObject((object) $norm);

        $this->notificationRepository->insert($notification);

        $nus = $this->notificationUserRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);
        // TODO Send email to référent opérationnel and responsable de traitement

        // Get referent operationnels for this collectivity
        $refs = $request->getCollectivity()->getUsers()->filter(function (User $u) {
            $mi = $u->getMoreInfos();

            return $mi && $mi[UserMoreInfoDictionary::MOREINFO_OPERATIONNAL];
        });
        if (0 === $refs->count()) {
            // No ref OP, get from collectivity
            $refs = [$request->getCollectivity()->getReferent()->getMail()];
        }
        // Add notification with email address for the référents
        foreach ($refs as $ref) {
            $nu = new NotificationUser();
            if (User::class === get_class($ref)) {
                $nu->setMail($ref->getEmail());
                $nu->setUser($ref);
            } else {
                $nu->setMail($ref);
            }

            $nu->setNotification($notification);
            $nu->setActive(true);
            $nu->setSent(false);
            $nu->setToken(sha1($ref->getId() . microtime() . mt_rand()));
            $this->notificationRepository->persist($nu);
        }
    }

    public function onNoLogin(NoLoginEvent $event)
    {
        // Send email to référent opérationnel
        // Add notification for DPO
        $user     = $event->getUser();
        $existing = $this->notificationRepository->findBy([
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
        $notification->setCreatedBy($user);
        $notification->setObject((object) $this->normalizer->normalize($user, null, $this->normalizerOptions()));
        $this->notificationRepository->insert($notification);

        // Get referent operationnels for this collectivity
        $refs = $user->getCollectivity()->getUsers()->filter(function (User $u) {
            $mi = $u->getMoreInfos();

            return $mi && $mi[UserMoreInfoDictionary::MOREINFO_OPERATIONNAL];
        });
        if (0 === $refs->count()) {
            // No ref OP, get from collectivity
            $refs = [$user->getCollectivity()->getReferent()->getMail()];
        }
        // Add notification with email address for the référents
        foreach ($refs as $ref) {
            $nu = new NotificationUser();
            if (User::class === get_class($ref)) {
                $nu->setMail($ref->getEmail());
                $nu->setUser($ref);
            } else {
                $nu->setMail($ref);
            }

            $nu->setNotification($notification);
            $nu->setActive(true);
            $nu->setSent(false);
            $this->notificationRepository->persist($nu);
        }
        // If no NotificationUser, this means the notification is for all DPOs
        // The emails will be sent with notifications:send command to all users that have a unsent NotificationUser with an email address
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
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH           => true,
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT   => 1,
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {
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
