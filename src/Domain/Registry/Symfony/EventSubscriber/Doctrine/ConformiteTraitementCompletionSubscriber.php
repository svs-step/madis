<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

use App\Domain\Registry\Calculator\Completion\ConformiteTraitementCompletion;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\ConformiteTraitement\Reponse;
use App\Domain\Registry\Model\Mesurement;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ConformiteTraitementCompletionSubscriber implements EventSubscriber
{
    /**
     * @var ConformiteTraitementCompletion
     */
    private $conformiteTraitementCompletion;

    public function __construct(ConformiteTraitementCompletion $conformiteTraitementCompletion)
    {
        $this->conformiteTraitementCompletion = $conformiteTraitementCompletion;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
            'postUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->processConformiteTraitement($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->processConformiteTraitement($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->processReponseOrMesurment($args);
    }

    private function processConformiteTraitement(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof ConformiteTraitement) {
            return;
        }

        $this->conformiteTraitementCompletion->setCalculsConformite($object);
    }

    private function processReponseOrMesurment(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Reponse && !$object instanceof Mesurement) {
            return;
        }

        $args->getObjectManager()->refresh($object);

        switch (true) {
            case $object instanceof Reponse:
                $this->conformiteTraitementCompletion->setCalculsConformite($object->getConformiteTraitement());
                $args->getObjectManager()->persist($object->getConformiteTraitement());
                break;
            case $object instanceof Mesurement:
                foreach ($object->getConformiteTraitementReponses() as $reponse) {
                    $this->conformiteTraitementCompletion->setCalculsConformite($reponse->getConformiteTraitement());
                    $args->getObjectManager()->persist($reponse->getConformiteTraitement());
                }
                break;
        }

        $args->getObjectManager()->flush();
    }
}
