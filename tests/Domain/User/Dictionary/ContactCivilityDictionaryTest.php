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
use App\Domain\User\Dictionary\ContactCivilityDictionary;
use PHPUnit\Framework\TestCase;

class ContactCivilityDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ContactCivilityDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new ContactCivilityDictionary();

        $this->assertEquals('user_contact_civility', $roleDictionary->getName());
        $this->assertEquals(ContactCivilityDictionary::getCivilities(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            ContactCivilityDictionary::CIVILITY_MISS   => 'Madame',
            ContactCivilityDictionary::CIVILITY_MISTER => 'Monsieur',
            ContactCivilityDictionary::CIVILITY_NONE   => '',
        ];

        $this->assertEquals($data, ContactCivilityDictionary::getCivilities());
    }

    public function testGetRolesKeys()
    {
        $data = [
            ContactCivilityDictionary::CIVILITY_MISS,
            ContactCivilityDictionary::CIVILITY_MISTER,
            ContactCivilityDictionary::CIVILITY_NONE,
        ];

        $this->assertEquals($data, ContactCivilityDictionary::getCivilitiesKeys());
    }
}
