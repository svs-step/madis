<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Notification\Event\ContractorEvent;
use App\Domain\Notification\Event\LateActionEvent;
use App\Domain\Notification\Event\ProofEvent;
use App\Domain\Notification\Event\TreatmentEvent;
use App\Domain\Notification\Event\ViolationEvent;
use App\Domain\Notification\Model\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationEventSubscriber implements EventSubscriberInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            TreatmentEvent::class => 'onTreatmentChanged',
            ContractorEvent::class => 'onContractorChanged',
            ViolationEvent::class => 'onViolationChanged',
            ProofEvent::class => 'onProofChanged',
            LateActionEvent::class => 'onLateAction',
        ];
    }

    public function onTreatmentChanged(TreatmentEvent $event)
    {

    }

    public function onLateAction(LateActionEvent $event)
    {
        $action = $event->getMesurement();
        ;
        $notification = new Notification();
        $notification->setModule("Actions");
        $notification->setCollectivity($action->getCollectivity());
    }
}