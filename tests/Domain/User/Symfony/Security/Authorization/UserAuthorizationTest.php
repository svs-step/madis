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

namespace App\Tests\Domain\User\Symfony\Security\Authorization;

use App\Domain\User\Model;
use App\Domain\User\Symfony\Security\Authorization\UserAuthorization;
use PHPUnit\Framework\TestCase;

class UserAuthorizationTest extends TestCase
{
    /**
     * @var UserAuthorization
     */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new UserAuthorization();
    }

    /**
     * Test canConnect.
     */
    public function testCanConnect(): void
    {
        $user         = new Model\User();
        $collectivity = new Model\Collectivity();
        $user->setCollectivity($collectivity);

        // User is enabled & collectivity active
        $user->setEnabled(true);
        $collectivity->setActive(true);
        $this->assertTrue($this->sut->canConnect($user));

        // User is disable & collectivity active
        $user->setEnabled(false);
        $collectivity->setActive(true);
        $this->assertFalse($this->sut->canConnect($user));

        // User is enabled & collectivity inactive
        $user->setEnabled(true);
        $collectivity->setActive(false);
        $this->assertFalse($this->sut->canConnect($user));
    }
}
