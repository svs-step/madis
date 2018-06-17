<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\User\Symfony\Security;

use App\Domain\User\Model\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * Checks the user account before authentication.
     *
     * @throws DisabledException
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isEnabledOrCollectivityActive()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);

            throw $ex;
        }
    }

    /**
     * Checks the user account after authentication.
     *
     * @throws AuthenticationException
     */
    public function checkPostAuth(UserInterface $user): void
    {
    }
}
