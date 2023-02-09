<?php

namespace App\Domain\Registry\Symfony\EventSubscriber\Kernel;

use App\Domain\Registry\Calculator\ConformiteOrganisationConformiteCalculator;
use App\Domain\Registry\Symfony\EventSubscriber\Event\ConformiteOrganisationEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConformiteOrganisationSubscriber implements EventSubscriberInterface
{
    /**
     * @var ConformiteOrganisationConformiteCalculator
     */
    private $calculator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ConformiteOrganisationConformiteCalculator $calculator, EntityManagerInterface $entityManager)
    {
        $this->calculator    = $calculator;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConformiteOrganisationEvent::class => ['calculConformite'],
        ];
    }

    public function calculConformite(ConformiteOrganisationEvent $event)
    {
        $this->calculator->calculEvaluationConformites($event->getEvaluation());

        $this->entityManager->flush();
    }
}
