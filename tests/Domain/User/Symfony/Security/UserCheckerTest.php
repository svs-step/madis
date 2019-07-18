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

namespace App\Tests\Domain\User\Symfony\Security;

use App\Domain\User\Model\User;
use App\Domain\User\Symfony\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserCheckerTest extends TestCase
{
    /**
     * @var UserChecker
     */
    private $checker;

    protected function setUp()
    {
        $this->checker = new UserChecker();
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
        $userProphecy = $this->prophesize(User::class);
        $userProphecy->isEnabledOrCollectivityActive()->shouldBeCalled()->willReturn(true);

        $this->checker->checkPreAuth($userProphecy->reveal());
    }

    /**
     * Test checkPreAuth
     * User isn't enabled or it collectivity not active.
     */
    public function testCheckPreAuthNotEnabledOrActive()
    {
        $this->expectException(DisabledException::class);

        $userProphecy = $this->prophesize(User::class);
        $userProphecy->isEnabledOrCollectivityActive()->shouldBeCalled()->willReturn(false);

        $this->checker->checkPreAuth($userProphecy->reveal());
    }
}
