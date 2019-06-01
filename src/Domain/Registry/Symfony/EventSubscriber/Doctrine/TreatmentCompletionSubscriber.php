<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Symfony\EventSubscriber\Doctrine;

use App\Domain\Registry\Calculator\Completion\TreatmentCompletion;
use App\Domain\Registry\Model\Treatment;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class TreatmentCompletionSubscriber implements EventSubscriber
{
    /**
     * @var TreatmentCompletion
     */
    private $treatmentCompletion;

    public function __construct(TreatmentCompletion $treatmentCompletion)
    {
        $this->treatmentCompletion = $treatmentCompletion;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->process($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->process($args);
    }

    private function process(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Treatment) {
            return;
        }

        $object->setCompletion($this->treatmentCompletion->calculate($object));
    }
}
