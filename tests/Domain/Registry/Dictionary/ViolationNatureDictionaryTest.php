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

use App\Domain\Registry\Dictionary\ViolationNatureDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ViolationNatureDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationNatureDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationNatureDictionary();

        $this->assertEquals('registry_violation_nature', $dictionary->getName());
        $this->assertEquals(ViolationNatureDictionary::getNatures(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationNatureDictionary::NATURE_CONFIDENTIALITY => 'Perte de la confidentialité',
            ViolationNatureDictionary::NATURE_INTEGRITY       => 'Perte de l\'intégrité',
            ViolationNatureDictionary::NATURE_AVAILABILITY    => 'Perte de la disponibilité',
        ];

        $this->assertEquals($data, ViolationNatureDictionary::getNatures());
        $this->assertEquals(
            \array_keys(ViolationNatureDictionary::getNatures()),
            ViolationNatureDictionary::getNaturesKeys()
        );
    }
}
