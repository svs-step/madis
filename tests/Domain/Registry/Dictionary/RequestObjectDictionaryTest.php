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

use App\Domain\Registry\Dictionary\RequestObjectDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class RequestObjectDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new RequestObjectDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new RequestObjectDictionary();

        $this->assertEquals('registry_request_object', $roleDictionary->getName());
        $this->assertEquals(RequestObjectDictionary::getObjects(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            RequestObjectDictionary::OBJECT_CORRECT          => 'Rectifier des données',
            RequestObjectDictionary::OBJECT_DELETE           => 'Supprimer des données',
            RequestObjectDictionary::OBJECT_WITHDRAW_CONSENT => 'Retirer le consentement',
            RequestObjectDictionary::OBJECT_ACCESS           => 'Accéder à des données',
            RequestObjectDictionary::OBJECT_DATA_PORTABILITY => 'Portabilité des données',
            RequestObjectDictionary::OBJECT_LIMIT_TREATMENT  => 'Limiter le traitement',
        ];

        $this->assertEquals($data, RequestObjectDictionary::getObjects());
    }

    public function testGetRolesKeys()
    {
        $data = [
            RequestObjectDictionary::OBJECT_CORRECT,
            RequestObjectDictionary::OBJECT_DELETE,
            RequestObjectDictionary::OBJECT_WITHDRAW_CONSENT,
            RequestObjectDictionary::OBJECT_ACCESS,
            RequestObjectDictionary::OBJECT_DATA_PORTABILITY,
            RequestObjectDictionary::OBJECT_LIMIT_TREATMENT,
        ];

        $this->assertEquals($data, RequestObjectDictionary::getObjectsKeys());
    }
}
