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

    public function __construct(
        NotificationRepository $notificationRepository,
        SerializerInterface $serializer,
        UserRepository $userRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->serializer             = $serializer;
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
        $existing = $this->notificationRepository->findBy([
            'module' => 'notification.modules.maturity',
            'collectivity' => $survey->getCollectivity(),
            'action' => 'notifications.actions.late_survey',
            'name' => $survey->__toString(),
        ]);
        if (count($existing)) {
            return;
        }
        $norm = $this->serializer->normalize($survey, null, [
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH                 => true,
            'depth_App\Domain\Maturity\Model\Survey::answers'          => 1,
            'depth_App\Domain\Maturity\Model\Survey::maturity'       => 1,
            'depth_App\Domain\Maturity\Model\Survey::collectivity'       => 1,
            'depth_App\Domain\Maturity\Model\Survey::creator'       => 1,
            'depth_App\Domain\Maturity\Model\Answer::questions'       => 1,
            'depth_App\Domain\Maturity\Model\Domain::questions'       => 1,
            'depth_App\Domain\Maturity\Model\Domain::maturity'       => 1,
            'depth_App\Domain\Maturity\Model\Answer::survey'       => 1,
            'depth_App\Domain\Maturity\Model\Question::answers'       => 1,
            'depth_App\Domain\Maturity\Model\Question::domain'       => 1,
            'depth_App\Domain\Maturity\Model\Maturity::survey'       => 1,
        ]);
        $notification = new Notification();
        $notification->setModule('notification.modules.maturity');
        $notification->setCollectivity($survey->getCollectivity());
        $notification->setAction('notifications.actions.late_survey');
        $notification->setName($survey->__toString());
        $notification->setObject($norm);
        $this->notificationRepository->insert($notification);

        $users = $this->userRepository->findNonDpoUsers();
        foreach($users as $user) {
            $notification = new Notification();
            $notification->setModule('notification.modules.maturity');
            $notification->setCollectivity($survey->getCollectivity());
            $notification->setAction('notifications.actions.late_survey');
            $notification->setName($survey->__toString());
            $notification->setUser($user);
            $notification->setObject($norm);
            $this->notificationRepository->insert($notification);
        }

        // TODO Send email to référent opérationnel
    }

    public function onLateAction(LateActionEvent $event)
    {
        $action       = $event->getMesurement();
        $existing = $this->notificationRepository->findBy([
            'module' => 'notification.modules.action',
            'collectivity' => $action->getCollectivity(),
            'action' => 'notifications.actions.late_action',
            'name' => $action->getName(),
        ]);
        if (count($existing)) {
            return;
        }

        $norm = $this->serializer->normalize($action, null, [
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH                 => true,
            'depth_App\Domain\Registry\Model\Mesurement::proofs'          => 1,
            'depth_App\Domain\Registry\Model\Mesurement::clonedFrom'       => 1,
            'depth_App\Domain\Registry\Model\Mesurement::conformiteOrganisation' => 1,
            'depth_App\Domain\Registry\Model\Mesurement::conformiteTraitementReponses'          => 1,
            'depth_App\Domain\Registry\Model\Mesurement::treatment'         => 1,
            'depth_App\Domain\Registry\Model\Mesurement::contractor'     => 1,
            'depth_App\Domain\Registry\Model\Mesurement::request'     => 1,
            'depth_App\Domain\Registry\Model\Mesurement::violation'     => 1,
        ]);
        $notification = new Notification();
        $notification->setModule('notification.modules.action');
        $notification->setCollectivity($action->getCollectivity());
        $notification->setAction('notifications.actions.late_action');
        $notification->setName($action->getName());
        $notification->setObject($norm);
        $this->notificationRepository->insert($notification);

        $users = $this->userRepository->findNonDpoUsers();
        foreach($users as $user) {
            $notification = new Notification();
            $notification->setModule('notification.modules.action');
            $notification->setCollectivity($action->getCollectivity());
            $notification->setAction('notifications.actions.late_action');
            $notification->setName($action->getName());
            $notification->setObject($norm);
            $notification->setUser($user);
            $this->notificationRepository->insert($notification);
        }

        // TODO Send email to référent opérationnel
    }

    public function onLateRequest(LateRequestEvent $event)
    {
        $request      = $event->getRequest();
        $existing = $this->notificationRepository->findBy([
            'module' => 'notification.modules.request',
            'collectivity' => $request->getCollectivity(),
            'action' => 'notifications.actions.late_request',
            'name' => $request->__toString(),
            'readAt' => null,
        ]);
        if (count($existing)) {
            return;
        }

        $norm = $this->serializer->normalize($request, null, [
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH                 => true,
            'depth_App\Domain\Registry\Model\Request::proofs'          => 1,
            'depth_App\Domain\Registry\Model\Request::applicant'       => 1,
            'depth_App\Domain\Registry\Model\Request::concernedPeople' => 1,
            'depth_App\Domain\Registry\Model\Request::answer'          => 1,
            'depth_App\Domain\Registry\Model\Request::service'         => 1,
            'depth_App\Domain\Registry\Model\Request::mesurements'     => 1,
        ]);

        $notification = new Notification();
        $notification->setModule('notification.modules.request');
        $notification->setCollectivity($request->getCollectivity());
        $notification->setAction('notifications.actions.late_request');
        $notification->setName($request->__toString());
        $notification->setObject($norm);
        $this->notificationRepository->insert($notification);

        $users = $this->userRepository->findNonDpoUsers();
        foreach($users as $user) {
            $notification = new Notification();
            $notification->setModule('notification.modules.request');
            $notification->setCollectivity($request->getCollectivity());
            $notification->setAction('notifications.actions.late_request');
            $notification->setName($request->__toString());
            $notification->setObject($norm);
            $notification->setUser($user);
            $this->notificationRepository->insert($notification);
        }

        // TODO Send email to référent opérationnel and responsable de traitement
    }

    public function onNoLogin(NoLoginEvent $event)
    {
        $user         = $event->getUser();
        $existing = $this->notificationRepository->findBy([
            'module' => 'notification.modules.user',
            'collectivity' => $user->getCollectivity(),
            'action' => 'notifications.actions.no_login',
            'name' => $user->getFullName(),
            'readAt' => null,
        ]);
        if (count($existing)) {
            return;
        }
        $notification = new Notification();

        $notification->setModule('notification.modules.user');
        $notification->setCollectivity($user->getCollectivity());
        $notification->setAction('notifications.actions.no_login');
        $notification->setName($user->getFullName());
        $notification->setObject($this->serializer->normalize($user, null, [
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($o) {return $o->getId(); },
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH         => true,
        ]));
        $this->notificationRepository->insert($notification);
    }
}
