<?php

declare(strict_types=1);

namespace App\Domain\Registry\Symfony\EventSubscriber\Doctrine;

use App\Domain\Registry\Calculator\ConformiteOrganisationConformiteCalculator;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class ConformiteOrganisationSubscriber implements EventSubscriber
{
    /**
     * @var ConformiteOrganisationConformiteCalculator
     */
    private $conformiteCalculator;

    public function __construct(ConformiteOrganisationConformiteCalculator $calculator)
    {
        $this->conformiteCalculator = $calculator;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->processConformiteOrganisation($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->processConformiteOrganisation($args);
    }

    private function processConformiteOrganisation(LifecycleEventArgs $args)
    {
        if (!($evaluation = $args->getObject()) instanceof Evaluation) {
            return;
        }

        $this->conformiteCalculator->calculEvaluationConformites($evaluation);
    }
}
