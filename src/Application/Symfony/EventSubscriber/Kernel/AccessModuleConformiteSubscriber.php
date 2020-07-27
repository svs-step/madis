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

namespace App\Application\Symfony\EventSubscriber\Kernel;

use App\Domain\Registry\Controller\ConformiteOrganisationController;
use App\Domain\Registry\Controller\ConformiteTraitementController;
use App\Domain\User\Model\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class AccessModuleConformiteSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController'],
            ],
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (!is_array($controller) || (is_array($controller) && !isset($controller[0])) || \is_null($user)) {
            return;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        switch (true) {
            case $controller[0] instanceof ConformiteTraitementController
                && !$user->getCollectivity()->isHasModuleConformiteTraitement():
                throw new AccessDeniedHttpException('You can\'t access to conformite des traitements');
                break;
            case $controller[0] instanceof ConformiteOrganisationController
                && !$user->getCollectivity()->isHasModuleConformiteOrganisation():
                throw new AccessDeniedHttpException('You can\'t access to conformite de l\'organisation');
                break;
        }
    }
}
