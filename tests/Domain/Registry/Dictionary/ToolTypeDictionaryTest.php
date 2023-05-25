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
use App\Domain\Registry\Dictionary\ToolTypeDictionary;
use PHPUnit\Framework\TestCase;

class ToolTypeDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ToolTypeDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ToolTypeDictionary();

        $this->assertEquals('registry_tool_type', $dictionary->getName());
        $this->assertEquals(ToolTypeDictionary::getTypes(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ToolTypeDictionary::SOFTWARE => 'Logiciel',
            ToolTypeDictionary::SUPPORT  => 'Support/Stockage',
        ];

        $this->assertEquals($data, ToolTypeDictionary::getTypes());
        $this->assertEquals(
            \array_keys(ToolTypeDictionary::getTypes()),
            ToolTypeDictionary::getTypesKeys()
        );
    }
}
