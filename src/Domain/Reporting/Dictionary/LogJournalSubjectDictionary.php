<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author ANODE <contact@agence-anode.fr>
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

namespace App\Domain\Reporting\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\Admin\Model\Duplication;
use App\Domain\Maturity\Model\Survey;
use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\Violation;
use App\Domain\User\Model\Collectivity;
use App\Domain\User\Model\Service;
use App\Domain\User\Model\User;

class LogJournalSubjectDictionary extends SimpleDictionary
{
    const ADMIN_DUPLICATION                           = 'admin_duplication';
    const MATURITY_SURVEY                             = 'maturity_survey';
    const REGISTRY_CONFORMITE_ORGANISATION_EVALUATION = 'registry_conformite_organisation_evaluation';
    const REGISTRY_CONFORMITE_TRAITEMENT              = 'registry_conformite_traitement';
    const REGISTRY_CONTRACTOR                         = 'registry_contractor';
    const REGISTRY_MESUREMENT                         = 'registry_mesurement';
    const REGISTRY_PROOF                              = 'registry_proof';
    const REGISTRY_REQUEST                            = 'registry_request';
    const REGISTRY_TREATMENT                          = 'registry_treatment';
    const USER_COLLECTIVITY                           = 'user_collectivity';
    const USER_EMAIL                                  = 'user_email';
    const USER_FIRSTNAME                              = 'user_firstname';
    const USER_LASTNAME                               = 'user_lastname';
    const USER_PASSWORD                               = 'user_password';
    const USER_USER                                   = 'user_user';
    const REGISTRY_VIOLATION                          = 'registry_violation';
    const USER_SERVICE                                = 'user_service';

    const CLASS_NAME_SUBJECT = [
        Duplication::class          => self::ADMIN_DUPLICATION,
        Survey::class               => self::MATURITY_SURVEY,
        Collectivity::class         => self::USER_COLLECTIVITY,
        Evaluation::class           => self::REGISTRY_CONFORMITE_ORGANISATION_EVALUATION,
        ConformiteTraitement::class => self::REGISTRY_CONFORMITE_TRAITEMENT,
        Contractor::class           => self::REGISTRY_CONTRACTOR,
        Mesurement::class           => self::REGISTRY_MESUREMENT,
        Proof::class                => self::REGISTRY_PROOF,
        Request::class              => self::REGISTRY_REQUEST,
        Treatment::class            => self::REGISTRY_TREATMENT,
        User::class                 => self::USER_USER,
        Violation::class            => self::REGISTRY_VIOLATION,
        Service::class              => self::USER_SERVICE,
    ];

    public function __construct()
    {
        parent::__construct('reporting_log_journal_subject', self::getSubjects());
    }

    /**
     * @return array
     */
    public static function getSubjects()
    {
        return [
            self::ADMIN_DUPLICATION                           => 'Duplication',
            self::REGISTRY_MESUREMENT                         => 'Action de protection',
            self::USER_COLLECTIVITY                           => 'Collectivité',
            self::REGISTRY_CONFORMITE_ORGANISATION_EVALUATION => 'Conformité organisation',
            self::REGISTRY_CONFORMITE_TRAITEMENT              => 'Conformité traitement',
            self::REGISTRY_REQUEST                            => 'Demande',
            self::USER_EMAIL                                  => 'Email utilisateur',
            self::MATURITY_SURVEY                             => 'Indice de maturité',
            self::USER_PASSWORD                               => 'Mdp utilisateur',
            self::USER_LASTNAME                               => 'Nom utilisateur',
            self::USER_FIRSTNAME                              => 'Prénom utilisateur',
            self::REGISTRY_PROOF                              => 'Preuve',
            self::REGISTRY_CONTRACTOR                         => 'Sous-traitant',
            self::REGISTRY_TREATMENT                          => 'Traitement',
            self::USER_USER                                   => 'Utilisateur',
            self::REGISTRY_VIOLATION                          => 'Violation',
            self::USER_SERVICE                                => 'Service',
        ];
    }

    /**
     * @return array
     */
    public static function getSubjectsKeys()
    {
        return \array_keys(self::getSubjects());
    }

    public static function getSubjectFromClassName(string $className): ?string
    {
        return isset(self::CLASS_NAME_SUBJECT[$className]) ? self::CLASS_NAME_SUBJECT[$className] : null;
    }

    public static function getSubjectLabelFromSubjectType(string $subjectKey)
    {
        return isset(self::getSubjects()[$subjectKey]) ? self::getSubjects()[$subjectKey] : null;
    }
}
