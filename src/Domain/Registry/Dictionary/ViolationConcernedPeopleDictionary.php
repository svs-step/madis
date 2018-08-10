<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan@awkan.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Dictionary;

use Knp\DictionaryBundle\Dictionary\SimpleDictionary;

class ViolationConcernedPeopleDictionary extends SimpleDictionary
{
    const PEOPLE_EMPLOYEE       = 'employee';
    const PEOPLE_USER           = 'user';
    const PEOPLE_MEMBER         = 'member';
    const PEOPLE_STUDENT        = 'student';
    const PEOPLE_MILITARY       = 'military';
    const PEOPLE_CUSTOMER       = 'customer';
    const PEOPLE_PATIENT        = 'patient';
    const PEOPLE_MINOR          = 'minor';
    const PEOPLE_VULNERABLE     = 'vulnerable';
    const PEOPLE_NOT_DETERMINED = 'not_determined';
    const PEOPLE_OTHER          = 'other';

    public function __construct()
    {
        parent::__construct('registry_violation_concerned_people', self::getConcernedPeople());
    }

    /**
     * Get an array of Concerned people.
     *
     * @return array
     */
    public static function getConcernedPeople()
    {
        return [
            self::PEOPLE_EMPLOYEE       => 'Employés',
            self::PEOPLE_USER           => 'Utilisateurs',
            self::PEOPLE_MEMBER         => 'Adhérents',
            self::PEOPLE_STUDENT        => 'Étudiants / élèves',
            self::PEOPLE_MILITARY       => 'Personnel militaire',
            self::PEOPLE_CUSTOMER       => 'Clients (actuels ou potentiels)',
            self::PEOPLE_PATIENT        => 'Patients',
            self::PEOPLE_MINOR          => 'Mineurs',
            self::PEOPLE_VULNERABLE     => 'Personnes vulnérables',
            self::PEOPLE_NOT_DETERMINED => 'Pas déterminé pour le moment',
            self::PEOPLE_OTHER          => 'Autres',
        ];
    }

    /**
     * Get keys of the Concerned people array.
     *
     * @return array
     */
    public static function getConcernedPeopleKeys()
    {
        return \array_keys(self::getConcernedPeople());
    }
}
