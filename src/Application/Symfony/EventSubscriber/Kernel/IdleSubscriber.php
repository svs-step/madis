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

namespace App\Application\Symfony\EventSubscriber\Kernel;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class IdleSubscriber
 * Check user idle time.
 * If user is idle more than accepted time, invalidate his session.
 */
class IdleSubscriber implements EventSubscriberInterface
{
    /**
     * @var int
     */
    private $expirationTime;

    /**
     * IdleSubscriber constructor.
     *
     * @param int $expirationTime Time in seconds to define idle
     */
    public function __construct(int $expirationTime)
    {
        $this->expirationTime = $expirationTime;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 9],
            ],
        ];
    }

    /**
     * OnKernelRequest check idle since last Request.
     * If idle is over, then invalidate session.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();

        $session->start();
        $metaData       = $session->getMetadataBag();
        $timeDifference = time() - $metaData->getLastUsed();

        if ($timeDifference > $this->expirationTime) {
            $session->invalidate();
        }
    }
}
