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

use App\Domain\User\Dictionary\ContactCivilityDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ContactCivilityDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ContactCivilityDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new ContactCivilityDictionary();

        $this->assertEquals('user_contact_civility', $roleDictionary->getName());
        $this->assertEquals(ContactCivilityDictionary::getCivilities(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            ContactCivilityDictionary::CIVILITY_MISS   => 'Madame',
            ContactCivilityDictionary::CIVILITY_MISTER => 'Monsieur',
        ];

        $this->assertEquals($data, ContactCivilityDictionary::getCivilities());
    }

    public function testGetRolesKeys()
    {
        $data = [
            ContactCivilityDictionary::CIVILITY_MISS,
            ContactCivilityDictionary::CIVILITY_MISTER,
        ];

        $this->assertEquals($data, ContactCivilityDictionary::getCivilitiesKeys());
    }
}
