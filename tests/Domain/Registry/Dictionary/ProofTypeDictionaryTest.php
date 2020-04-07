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
use App\Domain\Registry\Dictionary\ProofTypeDictionary;
use PHPUnit\Framework\TestCase;

class ProofTypeDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ProofTypeDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ProofTypeDictionary();

        $this->assertEquals('registry_proof_type', $dictionary->getName());
        $this->assertEquals(ProofTypeDictionary::getTypes(), $dictionary->getValues());
    }

    public function testGetTypes()
    {
        $data = [
            ProofTypeDictionary::TYPE_POLICY_MANAGEMENT        => 'Politique de Gestion',
            ProofTypeDictionary::TYPE_POLICY_PROTECTION        => 'Politique de Protection',
            ProofTypeDictionary::TYPE_CONCERNED_PEOPLE_REQUEST => 'Demande de personnes concernées',
            ProofTypeDictionary::TYPE_MESUREMENT               => 'Actions de protection',
            ProofTypeDictionary::TYPE_CERTIFICATION            => 'Attestations',
            ProofTypeDictionary::TYPE_IT_CHARTER               => 'Charte informatique',
            ProofTypeDictionary::TYPE_DELIBERATION             => 'Délibération',
            ProofTypeDictionary::TYPE_CONTRACT                 => 'Contrat',
            ProofTypeDictionary::TYPE_SENSITIZATION            => 'Sensibilisation',
            ProofTypeDictionary::TYPE_BALANCE_SHEET            => 'Bilan',
            ProofTypeDictionary::TYPE_OTHER                    => 'Autre',
        ];

        $this->assertEquals($data, ProofTypeDictionary::getTypes());
    }

    public function testGetTypesKeys()
    {
        $data = [
            ProofTypeDictionary::TYPE_POLICY_MANAGEMENT,
            ProofTypeDictionary::TYPE_POLICY_PROTECTION,
            ProofTypeDictionary::TYPE_CONCERNED_PEOPLE_REQUEST,
            ProofTypeDictionary::TYPE_MESUREMENT,
            ProofTypeDictionary::TYPE_CERTIFICATION,
            ProofTypeDictionary::TYPE_IT_CHARTER,
            ProofTypeDictionary::TYPE_DELIBERATION,
            ProofTypeDictionary::TYPE_CONTRACT,
            ProofTypeDictionary::TYPE_SENSITIZATION,
            ProofTypeDictionary::TYPE_BALANCE_SHEET,
            ProofTypeDictionary::TYPE_OTHER,
        ];

        $this->assertEquals($data, ProofTypeDictionary::getTypesKeys());
    }
}
