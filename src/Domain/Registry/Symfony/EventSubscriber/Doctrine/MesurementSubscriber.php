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

use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model\Mesurement;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class MesurementSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            'postUpdate',
        ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->process($args);
    }

    /**
     * On applied check all link reponse on conformiteTraitement to mark mesurement as not seen.
     */
    private function process(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Mesurement) {
            return;
        }

        $status = $object->getStatus();
        foreach ($object->getConformiteTraitementReponses() as $reponse) {
            if (MesurementStatusDictionary::STATUS_APPLIED === $status) {
                $reponse->addActionProtectionsPlanifiedNotSeen($object);
                $reponse->removeActionProtection($object);
            } else {
                $reponse->removeActionProtectionsPlanifiedNotSeen($object);
            }
            $args->getObjectManager()->persist($reponse);
        }

        $args->getObjectManager()->flush();
    }
}
