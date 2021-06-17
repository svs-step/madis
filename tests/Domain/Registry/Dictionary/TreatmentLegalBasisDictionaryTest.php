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
use App\Domain\Registry\Dictionary\TreatmentLegalBasisDictionary;
use PHPUnit\Framework\TestCase;

class TreatmentLegalBasisDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new TreatmentLegalBasisDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new TreatmentLegalBasisDictionary();

        $this->assertEquals('registry_treatment_legal_basis', $dictionary->getName());
        $this->assertEquals(TreatmentLegalBasisDictionary::getBasis(), $dictionary->getValues());
    }

    public function testGetBasis()
    {
        $data = [
            TreatmentLegalBasisDictionary::BASE_NONE                    => 'Aucune',
            TreatmentLegalBasisDictionary::BASE_CONSENT                 => 'Le consentement',
            TreatmentLegalBasisDictionary::BASE_LEGAL_OBLIGATION        => 'L\'obligation légale',
            TreatmentLegalBasisDictionary::BASE_CONTRACT_EXECUTION      => 'L\'exécution d\'un contrat',
            TreatmentLegalBasisDictionary::BASE_PUBLIC_INTEREST_MISSION => 'L\'exécution d\'une mission d\'intérêt public',
            TreatmentLegalBasisDictionary::BASE_LEGITIMATE_INTEREST     => 'L\'intérêt légitime',
            TreatmentLegalBasisDictionary::BASE_VITAL_INTEREST          => 'L\'intérêt vital',
        ];

        $this->assertEquals($data, TreatmentLegalBasisDictionary::getBasis());
    }

    public function testGetBasisKeys()
    {
        $data = [
            TreatmentLegalBasisDictionary::BASE_NONE,
            TreatmentLegalBasisDictionary::BASE_CONSENT,
            TreatmentLegalBasisDictionary::BASE_LEGAL_OBLIGATION,
            TreatmentLegalBasisDictionary::BASE_CONTRACT_EXECUTION,
            TreatmentLegalBasisDictionary::BASE_PUBLIC_INTEREST_MISSION,
            TreatmentLegalBasisDictionary::BASE_LEGITIMATE_INTEREST,
            TreatmentLegalBasisDictionary::BASE_VITAL_INTEREST,
        ];

        $this->assertEquals($data, TreatmentLegalBasisDictionary::getBasisKeys());
    }
}
