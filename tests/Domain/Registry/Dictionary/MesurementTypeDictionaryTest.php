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

use App\Domain\Registry\Dictionary\MesurementTypeDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class MesurementTypeDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new MesurementTypeDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new MesurementTypeDictionary();

        $this->assertEquals('registry_mesurement_type', $dictionary->getName());
        $this->assertEquals(MesurementTypeDictionary::getTypes(), $dictionary->getValues());
    }

    public function testGetTypes()
    {
        $data = [
            MesurementTypeDictionary::TYPE_LEGAL          => 'Juridique',
            MesurementTypeDictionary::TYPE_ORGANISATIONAL => 'Organisationnel',
            MesurementTypeDictionary::TYPE_TECHNICAL      => 'Technique',
        ];

        $this->assertEquals($data, MesurementTypeDictionary::getTypes());
    }

    public function testGetTypesKeys()
    {
        $data = [
            MesurementTypeDictionary::TYPE_LEGAL,
            MesurementTypeDictionary::TYPE_ORGANISATIONAL,
            MesurementTypeDictionary::TYPE_TECHNICAL,
        ];

        $this->assertEquals($data, MesurementTypeDictionary::getTypesKeys());
    }
}
