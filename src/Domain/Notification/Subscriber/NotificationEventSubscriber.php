<?php

namespace App\Domain\Notification\Subscriber;

use App\Application\Interfaces\CollectivityRelated;
use App\Domain\Notification\Dictionary\NotificationModuleDictionary;
use App\Domain\Notification\Event\ConformiteTraitementNeedsAIPDEvent;
use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\LateRequestEvent;
use App\Domain\Notification\Event\LateSurveyEvent;
use App\Domain\Notification\Event\NoLoginEvent;
use App\Domain\Notification\Model\Notification;
use App\Domain\Notification\Model\NotificationUser;
use App\Domain\Notification\Serializer\NotificationNormalizer;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\User\Dictionary\UserMoreInfoDictionary;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\User as UserRepository;
use App\Infrastructure\ORM\Notification\Repository\Notification as NotificationRepository;
use App\Infrastructure\ORM\Notification\Repository\NotificationUser as NotificationUserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This event subscriber creates notification for things that are trigerred by a cron job.
 */
class NotificationEventSubscriber implements EventSubscriberInterface
{
    protected NotificationRepository $notificationRepository;
    protected NotificationUserRepository $notificationUserRepository;
    protected NotificationNormalizer $normalizer;
    protected UserRepository $userRepository;
    protected TranslatorInterface $translator;
    protected string $requestDays;
    protected string $surveyDays;

    public function __construct(
        NotificationRepository $notificationRepository,
        NotificationUserRepository $notificationUserRepository,
        NotificationNormalizer $normalizer,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        string $requestDays,
        string $surveyDays,
    ) {
        $this->notificationRepository     = $notificationRepository;
        $this->notificationUserRepository = $notificationUserRepository;
        $this->normalizer                 = $normalizer;
        $this->userRepository             = $userRepository;
        $this->translator                 = $translator;
        $this->requestDays                = $requestDays;
        $this->surveyDays                 = $surveyDays;
    }

    public static function getSubscribedEvents()
    {
        return [
            LateActionEvent::class                    => 'onLateAction',
            LateRequestEvent::class                   => 'onLateRequest',
            NoLoginEvent::class                       => 'onNoLogin',
            LateSurveyEvent::class                    => 'onLateSurvey',
            ConformiteTraitementNeedsAIPDEvent::class => 'onNeedsAIPD',
        ];
    }

    public function onNeedsAIPD(ConformiteTraitementNeedsAIPDEvent $event)
    {
        $conformite = $event->getConformiteTraitement();

        $collectivity = $conformite->getTraitement()->getCollectivity();
        $existing     = $this->notificationRepository->findBy([
            'module'       => 'notification.modules.aipd',
            'collectivity' => $collectivity,
            'action'       => 'notifications.actions.treatment_needs_aipd',
            'name'         => $conformite->__toString(),
        ]);

        if ($existing && count($existing)) {
            return;
        }

        $norm  = $this->normalizer->normalize($conformite, null, self::normalizerOptions());
        $users = $this->userRepository->findNonDpoUsersForCollectivity($collectivity);

        $notification = new Notification();
        $notification->setModule('notification.modules.aipd');
        $notification->setCollectivity($collectivity);
        $notification->setAction('notifications.actions.treatment_needs_aipd');
        $notification->setName($conformite->__toString());
        $notification->setObject((object) $norm);
        $notification->setDpo(true);
        $notification->setSubject('');
        $this->notificationRepository->insert($notification);

        $nus = $this->notificationUserRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);
        $this->saveEmailNotificationForDPOs($notification, $conformite);
    }

    /**
     * Indice de maturité non réalisé depuis plus de...
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function onLateSurvey(LateSurveyEvent $event)
    {
        $survey   = $event->getSurvey();
        $existing = $this->notificationRepository->findBy([
            'module'       => 'notification.modules.maturity',
            'collectivity' => $survey->getCollectivity(),
            'action'       => 'notifications.actions.late_survey',
            'name'         => $survey->__toString(),
        ]);
        if ($existing && count($existing)) {
            return;
        }
        $norm = $this->normalizer->normalize($survey, null, self::normalizerOptions());

        $users = $this->userRepository->findNonDpoUsersForCollectivity($survey->getCollectivity());

        $notification = new Notification();
        $notification->setModule('notification.modules.maturity');
        $notification->setCollectivity($survey->getCollectivity());
        $notification->setAction('notifications.actions.late_survey');
        $notification->setName($survey->__toString());
        $notification->setObject((object) $norm);
        $notification->setDpo(true);
        $notification->setSubject($this->translator->trans('notifications.subject.late_survey', ['%days%' => $this->surveyDays]));
        $this->notificationRepository->insert($notification);
        $nus = $this->notificationUserRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);
    }

    /**
     * Action planifiée en retard.
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function onLateAction(LateActionEvent $event)
    {
        $action   = $event->getMesurement();
        $existing = $this->notificationRepository->findBy([
            'module'       => 'notification.modules.' . NotificationModuleDictionary::ACTION_PLAN,
            'collectivity' => $action->getCollectivity(),
            'action'       => 'notifications.actions.late_action',
            'name'         => $action->getName(),
        ]);
        if ($existing && count($existing)) {
            return;
        }

        $norm = $this->normalizer->normalize($action, null, self::normalizerOptions());

        $users        = $this->userRepository->findNonDpoUsersForCollectivity($action->getCollectivity());
        $notification = new Notification();
        $notification->setModule('notification.modules.' . NotificationModuleDictionary::ACTION_PLAN);
        $notification->setCollectivity($action->getCollectivity());
        $notification->setAction('notifications.actions.late_action');
        $notification->setName($action->getName());
        $notification->setObject((object) $norm);
        $notification->setDpo(true);
        $ob   = $notification->getObject();
        $date = \DateTime::createFromFormat(DATE_ATOM, $ob->planificationDate)->format('d/m/Y');

        $notification->setSubject($this->translator->trans('notifications.subject.late_action', ['%date%' => $date]));
        $this->notificationRepository->insert($notification);

        $nus = $this->notificationUserRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);

        // Send email to référent opérationnel
        $this->saveEmailNotificationForRefOp($notification, $action);
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
        if ($existing && count($existing)) {
            return;
        }

        $norm = $this->normalizer->normalize($request, null, self::normalizerOptions());

        $users = $this->userRepository->findNonDpoUsersForCollectivity($request->getCollectivity());

        $notification = new Notification();
        $notification->setModule('notification.modules.request');
        $notification->setCollectivity($request->getCollectivity());
        $notification->setAction('notifications.actions.late_request');
        $notification->setName($request->__toString());
        $notification->setObject((object) $norm);
        $notification->setDpo(true);
        $notification->setSubject($this->translator->trans('notifications.subject.late_request', ['%days%' => $this->requestDays]));
        $this->notificationRepository->insert($notification);

        $nus = $this->notificationUserRepository->saveUsers($notification, $users);

        $notification->setNotificationUsers($nus);
        $this->notificationRepository->update($notification);
        // Send email to référent opérationnel and responsable de traitement
        $this->saveEmailNotificationForRefOp($notification, $request);
        $this->saveEmailNotificationForRespTrait($notification, $request);
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
        // $notification->setCreatedBy($user);
        $notification->setObject((object) $this->normalizer->normalize($user, null, self::normalizerOptions()));
        $notification->setDpo(true);
        $notification->setSubject($this->translator->trans('notifications.subject.no_login'));
        $this->notificationRepository->insert($notification);

        $this->saveEmailNotificationForRefOp($notification, $user);
        // If no NotificationUser, this means the notification is for all DPOs
        // The emails will be sent with notifications:send command to all users that have a unsent NotificationUser with an email address
    }

    private function saveEmailNotificationForRefOp(Notification $notification, CollectivityRelated $object)
    {
        // Get referent operationnels for this collectivity
        $refs = $object->getCollectivity()->getUsers()->filter(function (User $u) {
            $mi = $u->getMoreInfos();

            return $mi && isset($mi[UserMoreInfoDictionary::MOREINFO_OPERATIONNAL]) && $mi[UserMoreInfoDictionary::MOREINFO_OPERATIONNAL];
        });
        if (0 === $refs->count()) {
            // No ref OP, get from collectivity
            if ($object->getCollectivity() && $object->getCollectivity()->getReferent()) {
                if ($object->getCollectivity()->getReferent()->getNotification()) {
                    $refs = [$object->getCollectivity()->getReferent()->getMail()];
                }
            }
        }
        // Add notification with email address for the référents
        $this->saveEmailNotifications($notification, $refs);
    }

    private function saveEmailNotificationForRespTrait(Notification $notification, CollectivityRelated $object)
    {
        // Get referent operationnels for this collectivity
        $refs = $object->getCollectivity()->getUsers()->filter(function (User $u) {
            $mi = $u->getMoreInfos();

            return $mi && isset($mi[UserMoreInfoDictionary::MOREINFO_TREATMENT]) && $mi[UserMoreInfoDictionary::MOREINFO_TREATMENT];
        });
        if (0 === $refs->count()) {
            // No ref OP, get from collectivity
            if ($object->getCollectivity() && $object->getCollectivity()->getLegalManager()) {
                if ($object->getCollectivity()->getLegalManager()->getNotification()) {
                    $refs = [$object->getCollectivity()->getLegalManager()->getMail()];
                }
            }
        }

        $this->saveEmailNotifications($notification, $refs);
    }

    private function saveEmailNotificationForDPOs(Notification $notification, ConformiteTraitement $object)
    {
        // Get DPOs
        $t    = $object->getTraitement();
        $refs = $t->getCollectivity()->getUsers()->filter(function (User $u) {
            $mi = $u->getMoreInfos();

            return in_array('ROLE_ADMIN', $u->getRoles())
                || in_array('ROLE_REFERENT', $u->getRoles())
                || ($mi && isset($mi[UserMoreInfoDictionary::MOREINFO_DPD]) && $mi[UserMoreInfoDictionary::MOREINFO_DPD]);
        });

        // Also get DPOs from collectivity
        if ($t->getCollectivity() && $t->getCollectivity()->getDpo()) {
            if ($t->getCollectivity()->getDpo()->getNotification()) {
                $refs[] = $t->getCollectivity()->getDpo()->getMail();
            }
        }

        $this->saveEmailNotifications($notification, $refs);
    }

    private function saveEmailNotifications(Notification $notification, $refs)
    {
        // Add notification with email address for the référents
        foreach ($refs as $ref) {
            $nu = new NotificationUser();
            if (is_object($ref) && User::class === get_class($ref)) {
                $nu->setMail($ref->getEmail());
                $nu->setUser($ref);
            } else {
                $nu->setMail($ref);
            }

            $nu->setToken(sha1($notification->getName() . microtime() . $nu->getMail()));
            $nu->setNotification($notification);
            $nu->setActive(false);
            $nu->setSent(false);
            $this->notificationUserRepository->persist($nu);
        }
    }

    public static function normalizerOptions(): array
    {
        return [
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH           => true,
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT   => 1,
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {
                return NotificationNormalizer::getObjectSimpleValue($o);
            },
            AbstractObjectNormalizer::MAX_DEPTH_HANDLER          => function ($o) {
                if (is_iterable($o)) {
                    $d = [];
                    foreach ($o as $item) {
                        $d[] = NotificationNormalizer::getObjectSimpleValue($item);
                    }

                    return $d;
                }

                return NotificationNormalizer::getObjectSimpleValue($o);
            },
        ];
    }
}
