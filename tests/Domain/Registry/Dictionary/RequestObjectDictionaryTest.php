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

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\Registry\Dictionary\RequestObjectDictionary;
use PHPUnit\Framework\TestCase;

class RequestObjectDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new RequestObjectDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new RequestObjectDictionary();

        $this->assertEquals('registry_request_object', $dictionary->getName());
        $this->assertEquals(RequestObjectDictionary::getObjects(), $dictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            RequestObjectDictionary::OBJECT_CORRECT          => 'Rectifier des données',
            RequestObjectDictionary::OBJECT_DELETE           => 'Supprimer des données',
            RequestObjectDictionary::OBJECT_WITHDRAW_CONSENT => 'Retirer le consentement',
            RequestObjectDictionary::OBJECT_ACCESS           => 'Accéder à des données',
            RequestObjectDictionary::OBJECT_DATA_PORTABILITY => 'Portabilité des données',
            RequestObjectDictionary::OBJECT_LIMIT_TREATMENT  => 'Limiter le traitement',
            RequestObjectDictionary::OBJECT_OTHER            => 'Autre',
        ];

        $this->assertEquals($data, RequestObjectDictionary::getObjects());
    }

    public function testGetRolesKeys()
    {
        $data = [
            RequestObjectDictionary::OBJECT_CORRECT,
            RequestObjectDictionary::OBJECT_DELETE,
            RequestObjectDictionary::OBJECT_WITHDRAW_CONSENT,
            RequestObjectDictionary::OBJECT_ACCESS,
            RequestObjectDictionary::OBJECT_DATA_PORTABILITY,
            RequestObjectDictionary::OBJECT_LIMIT_TREATMENT,
            RequestObjectDictionary::OBJECT_OTHER,
        ];

        $this->assertEquals($data, RequestObjectDictionary::getObjectsKeys());
    }
}
