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

namespace App\Domain\Notification\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\Documentation\Model\Document;
use App\Domain\Maturity\Model\Maturity;
use App\Domain\Notification\Model\Notification;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\ConformiteTraitement\ConformiteTraitement;
use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\Violation;

class NotificationModuleDictionary extends SimpleDictionary
{
    public function __construct()
    {
        parent::__construct('notificationModule', self::getModules());
    }

    /**
     * Get an array of Roles.
     *
     * @return array
     */
    public static function getModules()
    {
        return [
            Notification::MODULES[Treatment::class]            => 'Traitement',
            Notification::MODULES[Contractor::class]           => 'Sous-traitant',
            Notification::MODULES[Request::class]              => 'Demande',
            Notification::MODULES[Violation::class]            => 'Violation',
            Notification::MODULES[Proof::class]                => 'Preuve',
            Notification::MODULES[Mesurement::class]           => 'Action de protection',
            Notification::MODULES[Maturity::class]             => 'Indice de maturité',
            Notification::MODULES[ConformiteTraitement::class] => 'Conformité du traitement',
            Notification::MODULES[Conformite::class]           => 'Conformité de la structure',
            Notification::MODULES[AnalyseImpact::class]        => 'Analyse d\'impact',
            Notification::MODULES[Document::class]             => 'Espace Documentaire',
            'user'                                             => 'Utilisateurs',
        ];
    }

    /**
     * Get keys of the Roles array.
     *
     * @return array
     */
    public static function getModulesKeys()
    {
        return \array_keys(self::getModules());
    }
}
