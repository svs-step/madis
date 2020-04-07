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
use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use PHPUnit\Framework\TestCase;

class MesurementStatusDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new MesurementStatusDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new MesurementStatusDictionary();

        $this->assertEquals('registry_mesurement_status', $dictionary->getName());
        $this->assertEquals(MesurementStatusDictionary::getStatus(), $dictionary->getValues());
    }

    public function testGetStatus()
    {
        $data = [
            MesurementStatusDictionary::STATUS_APPLIED        => 'Appliquée',
            MesurementStatusDictionary::STATUS_NOT_APPLIED    => 'Non appliquée',
            MesurementStatusDictionary::STATUS_NOT_APPLICABLE => 'Non applicable',
        ];

        $this->assertEquals($data, MesurementStatusDictionary::getStatus());
    }

    public function testGetStatusKeys()
    {
        $data = [
            MesurementStatusDictionary::STATUS_APPLIED,
            MesurementStatusDictionary::STATUS_NOT_APPLIED,
            MesurementStatusDictionary::STATUS_NOT_APPLICABLE,
        ];

        $this->assertEquals($data, MesurementStatusDictionary::getStatusKeys());
    }
}
