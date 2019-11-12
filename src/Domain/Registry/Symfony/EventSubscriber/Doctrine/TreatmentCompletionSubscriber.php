<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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

    /**
     * Compute treatment completion.
     */
    private function process(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Treatment) {
            return;
        }

        $object->setCompletion($this->treatmentCompletion->calculate($object));
    }
}
