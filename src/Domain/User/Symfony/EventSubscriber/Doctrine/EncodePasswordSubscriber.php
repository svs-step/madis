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

use App\Domain\User\Model\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EncodePasswordSubscriber implements EventSubscriber
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * PrePersist
     * - User : If plainPassword is set, hash it and set password.
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        if (!$args->getObject() instanceof User) {
            return;
        }

        $this->encodePassword($args);
    }

    /**
     * PreUpdate
     * - User : If plainPassword is set, hash it and set password.
     *
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        if (!$args->getObject() instanceof User) {
            return;
        }

        $this->encodePassword($args);
    }

    /**
     * Encode plainPassword in password field.
     */
    public function encodePassword(LifecycleEventArgs $args): void
    {
        $model = $args->getObject();

        if (!$model instanceof User) {
            return;
        }

        if (\is_null($model->getPlainPassword())) {
            return;
        }

        $model->setPassword($this->passwordEncoder->encodePassword($model, $model->getPlainPassword()));
        $model->eraseCredentials();
    }
}
