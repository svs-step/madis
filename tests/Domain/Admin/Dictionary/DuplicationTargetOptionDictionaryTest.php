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

namespace App\Tests\Domain\Registry\Dictionary;

use App\Domain\Admin\Dictionary\DuplicationTargetOptionDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class DuplicationTargetOptionDictionaryTest extends TestCase
{
    public function testConstruct(): void
    {
        $dictionary = new DuplicationTargetOptionDictionary();

        $this->assertEquals(DuplicationTargetOptionDictionary::NAME, $dictionary->getName());
        $this->assertEquals(DuplicationTargetOptionDictionary::getData(), $dictionary->getValues());
    }

    /**
     * Test inheritance.
     */
    public function testInstanceOf(): void
    {
        $this->assertInstanceOf(SimpleDictionary::class, new DuplicationTargetOptionDictionary());
    }

    /**
     * !! Data Provider
     * Provide every expected data from dictionary.
     */
    public function dataProviderData(): array
    {
        return [
            [
                [
                    DuplicationTargetOptionDictionary::KEY_PER_TYPE         => 'Par type',
                    DuplicationTargetOptionDictionary::KEY_PER_COLLECTIVITY => 'Par liste de choix',
                ],
            ],
        ];
    }

    /**
     * Test getData
     * Check the dictionary data.
     *
     * @dataProvider dataProviderData
     */
    public function testGetData(array $data): void
    {
        $this->assertEquals(
            $data,
            DuplicationTargetOptionDictionary::getData()
        );
    }

    /**
     * test getDataKeys
     * Check the dictionary key data.
     *
     * @dataProvider dataProviderData
     */
    public function testGetDataKeys(array $data): void
    {
        $this->assertEquals(
            \array_keys($data),
            DuplicationTargetOptionDictionary::getDataKeys()
        );
    }
}
