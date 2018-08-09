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

use App\Domain\Registry\Dictionary\RequestCivilityDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class RequestCivilityDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new RequestCivilityDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new RequestCivilityDictionary();

        $this->assertEquals('registry_request_civility', $roleDictionary->getName());
        $this->assertEquals(RequestCivilityDictionary::getCivilities(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            RequestCivilityDictionary::CIVILITY_MISS   => 'Madame',
            RequestCivilityDictionary::CIVILITY_MISTER => 'Monsieur',
        ];

        $this->assertEquals($data, RequestCivilityDictionary::getCivilities());
    }

    public function testGetRolesKeys()
    {
        $data = [
            RequestCivilityDictionary::CIVILITY_MISS,
            RequestCivilityDictionary::CIVILITY_MISTER,
        ];

        $this->assertEquals($data, RequestCivilityDictionary::getCivilitiesKeys());
    }
}
