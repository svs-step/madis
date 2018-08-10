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
        $dictionary = new ProofTypeDictionary();

        $this->assertEquals('registry_proof_type', $dictionary->getName());
        $this->assertEquals(ProofTypeDictionary::getTypes(), $dictionary->getValues());
    }

    public function testGetTypes()
    {
        $data = [
            ProofTypeDictionary::TYPE_POLICY_MANAGEMENT        => 'Politique de Gestion',
            ProofTypeDictionary::TYPE_POLICY_PROTECTION        => 'Politique de Protection',
            ProofTypeDictionary::TYPE_CONCERNED_PEOPLE_REQUEST => 'Demande de personnes concernées',
            ProofTypeDictionary::TYPE_MESUREMENT               => 'Actions de protection',
            ProofTypeDictionary::TYPE_CERTIFICATION            => 'Attestations',
            ProofTypeDictionary::TYPE_IT_CHARTER               => 'Charte informatique',
            ProofTypeDictionary::TYPE_DELIBERATION             => 'Délibération',
            ProofTypeDictionary::TYPE_CONTRACT                 => 'Contrat',
            ProofTypeDictionary::TYPE_SENSITIZATION            => 'Sensibilisation',
            ProofTypeDictionary::TYPE_BALANCE_SHEET            => 'Bilan',
            ProofTypeDictionary::TYPE_OTHER                    => 'Autre',
        ];

        $this->assertEquals($data, ProofTypeDictionary::getTypes());
    }

    public function testGetTypesKeys()
    {
        $data = [
            ProofTypeDictionary::TYPE_POLICY_MANAGEMENT,
            ProofTypeDictionary::TYPE_POLICY_PROTECTION,
            ProofTypeDictionary::TYPE_CONCERNED_PEOPLE_REQUEST,
            ProofTypeDictionary::TYPE_MESUREMENT,
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
