<?php
/**
 * Created by PhpStorm.
 * User: bourlard
 * Date: 02/06/2018
 * Time: 11:32.
 */

namespace App\Tests\Domain\User\Dictionary;

use App\Domain\User\Dictionary\RoleDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class RoleDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new RoleDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new RoleDictionary();

        $this->assertEquals('user_role', $roleDictionary->getName());
        $this->assertEquals(RoleDictionary::getRoles(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            RoleDictionary::ROLE_PREVIEW => 'Lecteur',
            RoleDictionary::ROLE_USER    => 'Gestionnaire',
            RoleDictionary::ROLE_ADMIN   => 'Administrateur',
        ];

        $this->assertEquals($data, RoleDictionary::getRoles());
    }

    public function testGetRolesKeys()
    {
        $data = [
            RoleDictionary::ROLE_PREVIEW,
            RoleDictionary::ROLE_USER,
            RoleDictionary::ROLE_ADMIN,
        ];

        $this->assertEquals($data, RoleDictionary::getRolesKeys());
    }
}
