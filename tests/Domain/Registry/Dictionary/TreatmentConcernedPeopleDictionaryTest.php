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

use App\Domain\Registry\Dictionary\TreatmentConcernedPeopleDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class TreatmentConcernedPeopleDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new TreatmentConcernedPeopleDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new TreatmentConcernedPeopleDictionary();

        $this->assertEquals('registry_treatment_concerned_people', $dictionary->getName());
        $this->assertEquals(TreatmentConcernedPeopleDictionary::getTypes(), $dictionary->getValues());
    }

    public function testGetTypes()
    {
        $data = [
            TreatmentConcernedPeopleDictionary::TYPE_PARTICULAR => 'Particuliers',
            TreatmentConcernedPeopleDictionary::TYPE_USER       => 'Internautes',
            TreatmentConcernedPeopleDictionary::TYPE_AGENT      => 'Agents',
            TreatmentConcernedPeopleDictionary::TYPE_ELECTED    => 'Élus',
            TreatmentConcernedPeopleDictionary::TYPE_COMPANY    => 'Entreprises',
            TreatmentConcernedPeopleDictionary::TYPE_PARTNER    => 'Partenaires',
        ];

        $this->assertEquals($data, TreatmentConcernedPeopleDictionary::getTypes());
    }

    public function testGetTypesKeys()
    {
        $data = [
            TreatmentConcernedPeopleDictionary::TYPE_PARTICULAR,
            TreatmentConcernedPeopleDictionary::TYPE_USER,
            TreatmentConcernedPeopleDictionary::TYPE_AGENT,
            TreatmentConcernedPeopleDictionary::TYPE_ELECTED,
            TreatmentConcernedPeopleDictionary::TYPE_COMPANY,
            TreatmentConcernedPeopleDictionary::TYPE_PARTNER,
        ];

        $this->assertEquals($data, TreatmentConcernedPeopleDictionary::getTypesKeys());
    }
}
