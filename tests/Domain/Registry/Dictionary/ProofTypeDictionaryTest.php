<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Domain\Registry\Dictionary;

use App\Domain\Registry\Dictionary\ProofTypeDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ProofTypeDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ProofTypeDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new ProofTypeDictionary();

        $this->assertEquals('registry_proof_type', $roleDictionary->getName());
        $this->assertEquals(ProofTypeDictionary::getTypes(), $roleDictionary->getValues());
    }

    public function testGetTypes()
    {
        $data = [
            ProofTypeDictionary::TYPE_POLICY        => 'Politique',
            ProofTypeDictionary::TYPE_CERTIFICATION => 'Attestations',
            ProofTypeDictionary::TYPE_IT_CHARTER    => 'Charte informatique',
            ProofTypeDictionary::TYPE_DELIBERATION  => 'Délibération',
            ProofTypeDictionary::TYPE_CONTRACT      => 'Contrat',
            ProofTypeDictionary::TYPE_SENSITIZATION => 'Sensibilisation',
            ProofTypeDictionary::TYPE_BALANCE_SHEET => 'Bilan',
            ProofTypeDictionary::TYPE_OTHER         => 'Autre',
        ];

        $this->assertEquals($data, ProofTypeDictionary::getTypes());
    }

    public function testGetTypesKeys()
    {
        $data = [
            ProofTypeDictionary::TYPE_POLICY,
            ProofTypeDictionary::TYPE_CERTIFICATION,
            ProofTypeDictionary::TYPE_IT_CHARTER,
            ProofTypeDictionary::TYPE_DELIBERATION,
            ProofTypeDictionary::TYPE_CONTRACT,
            ProofTypeDictionary::TYPE_SENSITIZATION,
            ProofTypeDictionary::TYPE_BALANCE_SHEET,
            ProofTypeDictionary::TYPE_OTHER,
        ];

        $this->assertEquals($data, ProofTypeDictionary::getTypesKeys());
    }
}
