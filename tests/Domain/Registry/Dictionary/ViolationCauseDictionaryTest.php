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

use App\Domain\Registry\Dictionary\ViolationCauseDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ViolationCauseDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationCauseDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationCauseDictionary();

        $this->assertEquals('registry_violation_cause', $dictionary->getName());
        $this->assertEquals(ViolationCauseDictionary::getNatures(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationCauseDictionary::CAUSE_INTERNAL_MALICIOUS  => 'Acte interne malveillant',
            ViolationCauseDictionary::CAUSE_INTERNAL_ACCIDENTAL => 'Acte interne accidentel',
            ViolationCauseDictionary::CAUSE_EXTERNAL_MALICIOUS  => 'Acte externe malveillant',
            ViolationCauseDictionary::CAUSE_EXTERNAL_ACCIDENTAL => 'Acte externe accidentel',
            ViolationCauseDictionary::CAUSE_UNKNOWN             => 'Inconnu',
        ];

        $this->assertEquals($data, ViolationCauseDictionary::getNatures());
        $this->assertEquals(
            \array_keys(ViolationCauseDictionary::getNatures()),
            ViolationCauseDictionary::getNaturesKeys()
        );
    }
}
