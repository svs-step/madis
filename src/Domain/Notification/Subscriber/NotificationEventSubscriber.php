<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Notification\Event\ContractorEvent;
use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\ProofEvent;
use App\Domain\Notification\Event\TreatmentEvent;
use App\Domain\Notification\Event\ViolationEvent;
use App\Domain\Notification\Model\Notification;
use App\Domain\User\Repository\User as UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Infrastructure\ORM\Notification\Repository\Notification as NotificationRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * This event subscriber creates notification for things that are trigerred by a cron job
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
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            LateActionEvent::class => 'onLateAction',
        ];
    }

    public function onLateAction(LateActionEvent $event)
    {
        $action = $event->getMesurement();
        $notification = new Notification();
        $notification->setModule($this->translator->trans("notification.modules.action"));
        $notification->setCollectivity($action->getCollectivity());
        $notification->setName($action->getName());
        $notification->setObject($this->serializer->normalize($action, 'array', [
            'circular_reference_handler' => function($o) {return $o->getId();}
        ]));
        $this->notificationRepository->insert($notification);

        //Send email to référent opérationnel
    }
}