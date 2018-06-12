<?php
/**
 * Created by PhpStorm.
 * User: bourlard
 * Date: 02/06/2018
 * Time: 11:32.
 */

namespace App\Tests\Domain\User\Dictionary;

use App\Domain\User\Dictionary\CollectivityTypeDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class CollectivityTypeDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new CollectivityTypeDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new CollectivityTypeDictionary();

        $this->assertEquals('user_collectivity_type', $roleDictionary->getName());
        $this->assertEquals(CollectivityTypeDictionary::getTypes(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            CollectivityTypeDictionary::TYPE_COMMUNE            => 'Commune',
            CollectivityTypeDictionary::TYPE_CCAS               => 'CCAS',
            CollectivityTypeDictionary::TYPE_EPCI               => 'EPCI',
            CollectivityTypeDictionary::TYPE_CIAS               => 'CIAS',
            CollectivityTypeDictionary::TYPE_DEPARTMENTAL_UNION => 'Syndicat départemental',
            CollectivityTypeDictionary::TYPE_OTHER              => 'Autre',
        ];

        $this->assertEquals($data, CollectivityTypeDictionary::getTypes());
    }

    public function testGetRolesKeys()
    {
        $data = [
            CollectivityTypeDictionary::TYPE_COMMUNE,
            CollectivityTypeDictionary::TYPE_CCAS,
            CollectivityTypeDictionary::TYPE_EPCI,
            CollectivityTypeDictionary::TYPE_CIAS,
            CollectivityTypeDictionary::TYPE_DEPARTMENTAL_UNION,
            CollectivityTypeDictionary::TYPE_OTHER,
        ];

        $this->assertEquals($data, CollectivityTypeDictionary::getTypesKeys());
    }
}
