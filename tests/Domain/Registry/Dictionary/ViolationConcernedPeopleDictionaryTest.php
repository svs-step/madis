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

use App\Domain\Registry\Dictionary\ViolationConcernedPeopleDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ViolationConcernedPeopleDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationConcernedPeopleDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationConcernedPeopleDictionary();

        $this->assertEquals('registry_violation_concerned_people', $dictionary->getName());
        $this->assertEquals(ViolationConcernedPeopleDictionary::getConcernedPeople(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationConcernedPeopleDictionary::PEOPLE_EMPLOYEE       => 'Employés',
            ViolationConcernedPeopleDictionary::PEOPLE_USER           => 'Utilisateurs',
            ViolationConcernedPeopleDictionary::PEOPLE_MEMBER         => 'Adhérents',
            ViolationConcernedPeopleDictionary::PEOPLE_STUDENT        => 'Étudiants / élèves',
            ViolationConcernedPeopleDictionary::PEOPLE_MILITARY       => 'Personnel militaire',
            ViolationConcernedPeopleDictionary::PEOPLE_CUSTOMER       => 'Clients (actuels ou potentiels)',
            ViolationConcernedPeopleDictionary::PEOPLE_PATIENT        => 'Patients',
            ViolationConcernedPeopleDictionary::PEOPLE_MINOR          => 'Mineurs',
            ViolationConcernedPeopleDictionary::PEOPLE_VULNERABLE     => 'Personnes vulnérables',
            ViolationConcernedPeopleDictionary::PEOPLE_NOT_DETERMINED => 'Pas déterminé pour le moment',
            ViolationConcernedPeopleDictionary::PEOPLE_OTHER          => 'Autres',
        ];

        $this->assertEquals($data, ViolationConcernedPeopleDictionary::getConcernedPeople());
        $this->assertEquals(
            \array_keys(ViolationConcernedPeopleDictionary::getConcernedPeople()),
            ViolationConcernedPeopleDictionary::getConcernedPeopleKeys()
        );
    }
}
