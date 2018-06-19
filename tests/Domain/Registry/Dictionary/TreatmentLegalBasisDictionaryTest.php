<?php
/**
 * Created by PhpStorm.
 * User: bourlard
 * Date: 02/06/2018
 * Time: 11:32.
 */

namespace App\Tests\Domain\Registry\Dictionary;

use App\Domain\Registry\Dictionary\TreatmentLegalBasisDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class TreatmentLegalBasisDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new TreatmentLegalBasisDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new TreatmentLegalBasisDictionary();

        $this->assertEquals('registry_treatment_legal_basis', $roleDictionary->getName());
        $this->assertEquals(TreatmentLegalBasisDictionary::getBasis(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            TreatmentLegalBasisDictionary::BASE_CONSENT                 => 'Le consentement',
            TreatmentLegalBasisDictionary::BASE_LEGAL_OBLIGATION        => 'L\'obligation légale',
            TreatmentLegalBasisDictionary::BASE_CONTRACT_EXECUTION      => 'L\'exécution d\'un contrat',
            TreatmentLegalBasisDictionary::BASE_PUBLIC_INTEREST_MISSION => 'L\'exécution d\'une mission d\'intérêt publique',
            TreatmentLegalBasisDictionary::BASE_VITAL_INTEREST          => 'L\'intérêt vital',
        ];

        $this->assertEquals($data, TreatmentLegalBasisDictionary::getBasis());
    }

    public function testGetRolesKeys()
    {
        $data = [
            TreatmentLegalBasisDictionary::BASE_CONSENT,
            TreatmentLegalBasisDictionary::BASE_LEGAL_OBLIGATION,
            TreatmentLegalBasisDictionary::BASE_CONTRACT_EXECUTION,
            TreatmentLegalBasisDictionary::BASE_PUBLIC_INTEREST_MISSION,
            TreatmentLegalBasisDictionary::BASE_VITAL_INTEREST,
        ];

        $this->assertEquals($data, TreatmentLegalBasisDictionary::getBasisKeys());
    }
}
