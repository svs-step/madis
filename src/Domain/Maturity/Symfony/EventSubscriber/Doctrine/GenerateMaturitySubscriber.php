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

namespace App\Domain\Maturity\Symfony\EventSubscriber\Doctrine;

use App\Domain\Maturity\Calculator;
use App\Domain\Maturity\Model;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class GenerateMaturitySubscriber implements EventSubscriber
{
    /**
     * @var Calculator\MaturityHandler
     */
    private $maturityHandler;

    public function __construct(
        Calculator\MaturityHandler $maturityHandler
    ) {
        $this->maturityHandler = $maturityHandler;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    /**
     * Handle maturity for each persisted or updated survey.
     *
     * @param LifecycleEventArgs $args
     */
    private function process(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Model\Survey) {
            return;
        }

        $this->maturityHandler->handle($object);
    }
}
