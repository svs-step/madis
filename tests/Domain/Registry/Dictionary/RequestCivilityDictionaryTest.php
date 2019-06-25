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

namespace App\Tests\Domain\Registry\Dictionary;

use App\Domain\Registry\Dictionary\RequestCivilityDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class RequestCivilityDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new RequestCivilityDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new RequestCivilityDictionary();

        $this->assertEquals('registry_request_civility', $dictionary->getName());
        $this->assertEquals(RequestCivilityDictionary::getCivilities(), $dictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            RequestCivilityDictionary::CIVILITY_MISS   => 'Madame',
            RequestCivilityDictionary::CIVILITY_MISTER => 'Monsieur',
        ];

        $this->assertEquals($data, RequestCivilityDictionary::getCivilities());
    }

    public function testGetRolesKeys()
    {
        $data = [
            RequestCivilityDictionary::CIVILITY_MISS,
            RequestCivilityDictionary::CIVILITY_MISTER,
        ];

        $this->assertEquals($data, RequestCivilityDictionary::getCivilitiesKeys());
    }
}
