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

use App\Domain\Registry\Dictionary\DelayPeriodDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class DelayPeriodDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new DelayPeriodDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new DelayPeriodDictionary();

        $this->assertEquals('registry_delay_period', $dictionary->getName());
        $this->assertEquals(DelayPeriodDictionary::getPeriods(), $dictionary->getValues());
    }

    public function testGetPeriods()
    {
        $data = [
            DelayPeriodDictionary::PERIOD_DAY   => 'Jour(s)',
            DelayPeriodDictionary::PERIOD_MONTH => 'Mois',
            DelayPeriodDictionary::PERIOD_YEAR  => 'An(s)',
        ];

        $this->assertEquals($data, DelayPeriodDictionary::getPeriods());
    }

    public function testGetPeriodsKeys()
    {
        $data = [
            DelayPeriodDictionary::PERIOD_DAY,
            DelayPeriodDictionary::PERIOD_MONTH,
            DelayPeriodDictionary::PERIOD_YEAR,
        ];

        $this->assertEquals($data, DelayPeriodDictionary::getPeriodsKeys());
    }
}
