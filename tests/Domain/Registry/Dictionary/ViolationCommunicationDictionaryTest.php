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
use App\Domain\Registry\Dictionary\ViolationCommunicationDictionary;
use PHPUnit\Framework\TestCase;

class ViolationCommunicationDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationCommunicationDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationCommunicationDictionary();

        $this->assertEquals('registry_violation_communication', $dictionary->getName());
        $this->assertEquals(ViolationCommunicationDictionary::getCommunications(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationCommunicationDictionary::YES  => 'Oui, les personnes ont été informées',
            ViolationCommunicationDictionary::SOON => 'Non, mais elles le seront',
            ViolationCommunicationDictionary::NO   => 'Non ils ne le seront pas',
        ];

        $this->assertEquals($data, ViolationCommunicationDictionary::getCommunications());
        $this->assertEquals(
            \array_keys(ViolationCommunicationDictionary::getCommunications()),
            ViolationCommunicationDictionary::getCommunicationsKeys()
        );
    }
}
