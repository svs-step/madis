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

use App\Domain\Registry\Dictionary\ViolationCommunicationDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ViolationCommunicationDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationCommunicationDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationCommunicationDictionary();

        $this->assertEquals('registry_violation_communication', $dictionary->getName());
        $this->assertEquals(ViolationCommunicationDictionary::getCommunications(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationCommunicationDictionary::YES  => 'Oui, les personnes ont été informées',
            ViolationCommunicationDictionary::SOON => 'Non, mais elles le seront',
            ViolationCommunicationDictionary::NO   => 'Non ils ne le seront pas',
        ];

        $this->assertEquals($data, ViolationCommunicationDictionary::getCommunications());
        $this->assertEquals(
            \array_keys(ViolationCommunicationDictionary::getCommunications()),
            ViolationCommunicationDictionary::getCommunicationsKeys()
        );
    }
}
