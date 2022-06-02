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

namespace App\Domain\User\Symfony\EventSubscriber\Kernel;

use App\Application\Controller\ControllerHelper;
use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Model\LogJournal;
use App\Domain\User\Dictionary\UserRoleDictionary;
use App\Domain\User\Event\ExceededLoginAttempts;
use App\Domain\User\Exception\ExceededLoginAttemptsException;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private ControllerHelper $helper;

    public function __construct(ControllerHelper $helper) {
        $this->helper = $helper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }



    public function onException(ExceptionEvent $event)
    {
        if ($event->getThrowable() instanceof ExceededLoginAttemptsException) {
            $view = $this->helper->render('User/Security/locked.html.twig');
            $event->setResponse($view);
        }
    }
}
