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

use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class MesurementStatusDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new MesurementStatusDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new MesurementStatusDictionary();

        $this->assertEquals('registry_mesurement_status', $roleDictionary->getName());
        $this->assertEquals(MesurementStatusDictionary::getStatus(), $roleDictionary->getValues());
    }

    public function testGetStatus()
    {
        $data = [
            MesurementStatusDictionary::STATUS_APPLIED        => 'Appliquée',
            MesurementStatusDictionary::STATUS_NOT_APPLIED    => 'Non appliquée',
            MesurementStatusDictionary::STATUS_NOT_APPLICABLE => 'Non applicable',
        ];

        $this->assertEquals($data, MesurementStatusDictionary::getStatus());
    }

    public function testGetStatusKeys()
    {
        $data = [
            MesurementStatusDictionary::STATUS_APPLIED,
            MesurementStatusDictionary::STATUS_NOT_APPLIED,
            MesurementStatusDictionary::STATUS_NOT_APPLICABLE,
        ];

        $this->assertEquals($data, MesurementStatusDictionary::getStatusKeys());
    }
}
