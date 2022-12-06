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

namespace App\Domain\User\Symfony\EventSubscriber\Doctrine;

use App\Domain\User\Component\TokenGenerator;
use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DefinePasswordSubscriber implements EventSubscriber
{
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(
        TokenGenerator $tokenGenerator
    ) {
        $this->tokenGenerator = $tokenGenerator;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
        ];
    }

    /**
     * PrePersist
     * - User : If no plainPassword, randomize it.
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $model = $args->getObject();
        if (!$model instanceof User) {
            return;
        }

        // No password is set, randomize one
        if (\is_null($model->getPlainPassword()) && \is_null($model->getPassword())) {
            $model->setPlainPassword($this->tokenGenerator->generateToken());
        }
    }
}
