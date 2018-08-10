<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
