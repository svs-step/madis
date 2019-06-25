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
