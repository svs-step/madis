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

namespace App\Tests\Domain\User\Symfony\Security\Checker;

use App\Domain\User\Model\User;
use App\Domain\User\Symfony\Security\Authorization\UserAuthorization;
use App\Domain\User\Symfony\Security\Checker\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserCheckerTest extends TestCase
{
    /**
     * @var UserAuthorization
     */
    private $userAuthorizationProphecy;

    /**
     * @var UserChecker
     */
    private $checker;

    protected function setUp()
    {
        $this->userAuthorizationProphecy = $this->prophesize(UserAuthorization::class);

        $this->checker = new UserChecker(
            $this->userAuthorizationProphecy->reveal()
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(UserCheckerInterface::class, $this->checker);
    }

    /**
     * Test checkPreAuth.
     */
    public function testCheckPreAuth()
    {
        $user = new User();

        $this->userAuthorizationProphecy->canConnect($user)->shouldBeCalled()->willReturn(true);

        $this->checker->checkPreAuth($user);
    }

    /**
     * Test checkPreAuth
     * User isn't enabled or it collectivity not active.
     */
    public function testCheckPreAuthNotEnabledOrActive()
    {
        $user = new User();
        $this->expectException(DisabledException::class);

        $this->userAuthorizationProphecy->canConnect($user)->shouldBeCalled()->willReturn(false);

        $this->checker->checkPreAuth($user);
    }
}
