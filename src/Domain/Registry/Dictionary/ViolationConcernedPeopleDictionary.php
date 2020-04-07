<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\Registry\Dictionary;

use App\Application\Dictionary\SimpleDictionary;

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
