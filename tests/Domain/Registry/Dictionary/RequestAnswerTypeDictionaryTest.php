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

use App\Domain\Registry\Dictionary\RequestAnswerTypeDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class RequestAnswerTypeDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new RequestAnswerTypeDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new RequestAnswerTypeDictionary();

        $this->assertEquals('registry_request_answer_type', $roleDictionary->getName());
        $this->assertEquals(RequestAnswerTypeDictionary::getTypes(), $roleDictionary->getValues());
    }

    public function testGetTypes()
    {
        $data = [
            RequestAnswerTypeDictionary::TYPE_MAIL   => 'Mail',
            RequestAnswerTypeDictionary::TYPE_POSTAL => 'Courrier postal',
            RequestAnswerTypeDictionary::TYPE_DIRECT => 'Remis en main propre',
        ];

        $this->assertEquals($data, RequestAnswerTypeDictionary::getTypes());
    }

    public function testGetTypesKeys()
    {
        $data = [
            RequestAnswerTypeDictionary::TYPE_MAIL,
            RequestAnswerTypeDictionary::TYPE_POSTAL,
            RequestAnswerTypeDictionary::TYPE_DIRECT,
        ];

        $this->assertEquals($data, RequestAnswerTypeDictionary::getTypesKeys());
    }
}
