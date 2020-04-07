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
use App\Domain\User\Dictionary\CollectivityTypeDictionary;
use PHPUnit\Framework\TestCase;

class CollectivityTypeDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new CollectivityTypeDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new CollectivityTypeDictionary();

        $this->assertEquals('user_collectivity_type', $roleDictionary->getName());
        $this->assertEquals(CollectivityTypeDictionary::getTypes(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            CollectivityTypeDictionary::TYPE_COMMUNE            => 'Commune',
            CollectivityTypeDictionary::TYPE_CCAS               => 'CCAS',
            CollectivityTypeDictionary::TYPE_EPCI               => 'EPCI',
            CollectivityTypeDictionary::TYPE_CIAS               => 'CIAS',
            CollectivityTypeDictionary::TYPE_DEPARTMENTAL_UNION => 'Syndicat départemental',
            CollectivityTypeDictionary::TYPE_OTHER              => 'Autre',
        ];

        $this->assertEquals($data, CollectivityTypeDictionary::getTypes());
    }

    public function testGetRolesKeys()
    {
        $data = [
            CollectivityTypeDictionary::TYPE_COMMUNE,
            CollectivityTypeDictionary::TYPE_CCAS,
            CollectivityTypeDictionary::TYPE_EPCI,
            CollectivityTypeDictionary::TYPE_CIAS,
            CollectivityTypeDictionary::TYPE_DEPARTMENTAL_UNION,
            CollectivityTypeDictionary::TYPE_OTHER,
        ];

        $this->assertEquals($data, CollectivityTypeDictionary::getTypesKeys());
    }
}
