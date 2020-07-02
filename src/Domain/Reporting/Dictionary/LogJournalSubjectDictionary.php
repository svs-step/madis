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

class LogJournalSubjectDictionary extends SimpleDictionary
{
    const MATURITY_SURVEY                             = 'maturity_survey';
    const REGISTRY_COLLECTIVITY                       = 'registry_collectivity';
    const REGISTRY_CONFORMITE_ORGANISATION_EVALUATION = 'registry_conformite_organisation_evaluation';
    const REGISTRY_CONFORMITE_TRAITEMENT              = 'registry_conformite_traitement';
    const REGISTRY_CONTRACTOR                         = 'registry_contractor';
    const REGISTRY_MESUREMENT                         = 'registry_mesurement';
    const REGISTRY_PROOF                              = 'registry_proof';
    const REGISTRY_REQUEST                            = 'registry_request';
    const REGISTRY_TREATMENT                          = 'registry_treatment';
    const USER_EMAIL                                  = 'user_email';
    const USER_FIRSTNAME                              = 'user_firstname';
    const USER_LASTNAME                               = 'user_lastname';
    const USER_MDP                                    = 'user_mdp';
    const USER_USER                                   = 'user_user';
    const REGISTRY_VIOLATION                          = 'registry_violation';

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
            self::MATURITY_SURVEY                             => 'Indice de maturité',
            self::REGISTRY_COLLECTIVITY                       => 'Collectivité',
            self::REGISTRY_CONFORMITE_ORGANISATION_EVALUATION => 'Evaluation',
            self::REGISTRY_CONFORMITE_TRAITEMENT              => 'Conformité traitement',
            self::REGISTRY_CONTRACTOR                         => 'Sous-traitant',
            self::REGISTRY_MESUREMENT                         => 'Action de protection',
            self::REGISTRY_PROOF                              => 'Preuve',
            self::REGISTRY_REQUEST                            => 'Demande',
            self::REGISTRY_TREATMENT                          => 'Traitement',
            self::USER_EMAIL                                  => 'Email utilisateur',
            self::USER_FIRSTNAME                              => 'Prénom utilisateur',
            self::USER_LASTNAME                               => 'Nom utilisateur',
            self::USER_MDP                                    => 'Mdp utilisateur',
            self::USER_USER                                   => 'Utilisateur',
            self::REGISTRY_VIOLATION                          => 'Violation',
        ];
    }

    /**
     * @return array
     */
    public static function getSubjectsKeys()
    {
        return \array_keys(self::getSubjects());
    }
}
