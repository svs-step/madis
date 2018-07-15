<?php
/**
 * Created by PhpStorm.
 * User: bourlard
 * Date: 02/06/2018
 * Time: 11:32.
 */

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
        $roleDictionary = new MesurementTypeDictionary();

        $this->assertEquals('registry_mesurement_type', $roleDictionary->getName());
        $this->assertEquals(MesurementTypeDictionary::getTypes(), $roleDictionary->getValues());
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
