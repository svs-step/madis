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

namespace App\Tests\Domain\User\Dictionary;

use App\Domain\User\Dictionary\UserRoleDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class UserRoleDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new UserRoleDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new UserRoleDictionary();

        $this->assertEquals('user_user_role', $roleDictionary->getName());
        $this->assertEquals(UserRoleDictionary::getRoles(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            UserRoleDictionary::ROLE_PREVIEW => 'Lecteur',
            UserRoleDictionary::ROLE_USER    => 'Gestionnaire',
            UserRoleDictionary::ROLE_ADMIN   => 'Administrateur',
        ];

        $this->assertEquals($data, UserRoleDictionary::getRoles());
    }

    public function testGetRolesKeys()
    {
        $data = [
            UserRoleDictionary::ROLE_PREVIEW,
            UserRoleDictionary::ROLE_USER,
            UserRoleDictionary::ROLE_ADMIN,
        ];

        $this->assertEquals($data, UserRoleDictionary::getRolesKeys());
    }
}
