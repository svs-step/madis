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

use App\Domain\Registry\Dictionary\ViolationImpactDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ViolationImpactDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationImpactDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationImpactDictionary();

        $this->assertEquals('registry_violation_impact', $dictionary->getName());
        $this->assertEquals(ViolationImpactDictionary::getImpacts(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationImpactDictionary::IMPACT_LOSS_CONTROL_PERSONAL_DATA        => 'Perte de contrôle sur leurs données personnelles',
            ViolationImpactDictionary::IMPACT_LIMITATION_RIGHT                  => 'Limitation de leurs droits',
            ViolationImpactDictionary::IMPACT_DISCRIMINATION                    => 'Discrimination',
            ViolationImpactDictionary::IMPACT_IDENTITY_THEFT                    => 'Vol d\'identité',
            ViolationImpactDictionary::IMPACT_FRAUD                             => 'Fraude',
            ViolationImpactDictionary::IMPACT_UNAUTHORIZED_PSEUDO_LIFTING       => 'Levée non autorisée de pseudonimisation',
            ViolationImpactDictionary::IMPACT_FINANCIAL_LOSSES                  => 'Pertes financières',
            ViolationImpactDictionary::IMPACT_REPUTATION_DAMAGE                 => 'Atteinte à la réputation',
            ViolationImpactDictionary::IMPACT_LOSS_PROFESSIONAL_CONFIDENTIALITY => 'Perte de la confidentialité de données protégées par un secret professionnel',
            ViolationImpactDictionary::IMPACT_OTHER                             => 'Autre',
        ];

        $this->assertEquals($data, ViolationImpactDictionary::getImpacts());
        $this->assertEquals(
            \array_keys(ViolationImpactDictionary::getImpacts()),
            ViolationImpactDictionary::getImpactsKeys()
        );
    }
}
