<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Maturity\Symfony\EventSubscriber;

use App\Domain\Maturity\Calculator;
use App\Domain\Maturity\Model;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class GenerateMaturitySubscriber implements EventSubscriber
{
    /**
     * @var Calculator\Maturity
     */
    private $calculator;

    public function __construct(
        Calculator\Maturity $calculator
    ) {
        $this->calculator = $calculator;
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
        $this->process($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->process($args);
    }

    private function process(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Model\Survey) {
            return;
        }

        // Calculate & generate score by maturity
        $maturityList = $this->calculator->generateMaturityByDomain($object);
        $object->setMaturity($maturityList);
        $object->setScore($this->calculator->getGlobalScore($maturityList));
    }
}
