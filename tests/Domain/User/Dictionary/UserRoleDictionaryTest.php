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

namespace App\Tests\Domain\User\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\User\Dictionary\UserRoleDictionary;
use PHPUnit\Framework\TestCase;

class UserRoleDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new UserRoleDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new UserRoleDictionary();

        $this->assertEquals('user_user_role', $roleDictionary->getName());
        $this->assertEquals(UserRoleDictionary::getRoles(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            UserRoleDictionary::ROLE_PREVIEW => 'Lecteur',
            UserRoleDictionary::ROLE_USER    => 'Gestionnaire',
            UserRoleDictionary::ROLE_ADMIN   => 'Administrateur',
        ];

        $this->assertEquals($data, UserRoleDictionary::getRoles());
    }

    public function testGetRolesKeys()
    {
        $data = [
            UserRoleDictionary::ROLE_PREVIEW,
            UserRoleDictionary::ROLE_USER,
            UserRoleDictionary::ROLE_ADMIN,
        ];

        $this->assertEquals($data, UserRoleDictionary::getRolesKeys());
    }
}
