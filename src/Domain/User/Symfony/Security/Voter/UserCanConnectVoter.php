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

namespace App\Domain\User\Symfony\Security\Voter;

use App\Domain\User\Model\User;
use App\Domain\User\Symfony\Security\Authorization\UserAuthorization;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserCanConnectVoter extends Voter
{
    const CAN_CONNECT = 'CAN_CONNECT';

    /**
     * @var UserAuthorization
     */
    private $userAuthorization;

    /**
     * UserCanConnectVoter constructor.
     */
    public function __construct(UserAuthorization $userAuthorization)
    {
        $this->userAuthorization = $userAuthorization;
    }

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (self::CAN_CONNECT !== $attribute) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param User   $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return $this->userAuthorization->canConnect($subject);
    }
}
