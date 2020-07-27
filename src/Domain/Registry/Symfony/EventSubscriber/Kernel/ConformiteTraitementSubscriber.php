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

namespace App\Domain\Registry\Symfony\EventSubscriber\Kernel;

use App\Domain\Registry\Symfony\EventSubscriber\Event\ConformiteTraitementEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConformiteTraitementSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->entityManager = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConformiteTraitementEvent::class => ['resetAllReponseMesurementNotSeen'],
        ];
    }

    public function resetAllReponseMesurementNotSeen(ConformiteTraitementEvent $event)
    {
        foreach ($event->getConformiteTraitement()->getReponses() as $reponse) {
            $reponse->resetActionProtectionsPlanifiedNotSeens();
            $this->entityManager->persist($reponse);
        }

        $this->entityManager->flush();
    }
}
