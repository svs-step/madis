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

use App\Domain\Registry\Dictionary\ViolationGravityDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ViolationGravityDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationGravityDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationGravityDictionary();

        $this->assertEquals('registry_violation_gravity', $dictionary->getName());
        $this->assertEquals(ViolationGravityDictionary::getGravities(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationGravityDictionary::GRAVITY_NEGLIGIBLE => 'Négligeable',
            ViolationGravityDictionary::GRAVITY_LIMITED    => 'Limité',
            ViolationGravityDictionary::GRAVITY_IMPORTANT  => 'Important',
            ViolationGravityDictionary::GRAVITY_MAXIMUM    => 'Maximal',
        ];

        $this->assertEquals($data, ViolationGravityDictionary::getGravities());
        $this->assertEquals(
            \array_keys(ViolationGravityDictionary::getGravities()),
            ViolationGravityDictionary::getGravitiesKeys()
        );
    }
}
