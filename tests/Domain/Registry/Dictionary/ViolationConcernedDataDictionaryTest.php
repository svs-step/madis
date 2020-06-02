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

namespace App\Tests\Domain\Registry\Dictionary;

use App\Application\Dictionary\SimpleDictionary;
use App\Domain\Registry\Dictionary\ViolationConcernedDataDictionary;
use PHPUnit\Framework\TestCase;

class ViolationConcernedDataDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationConcernedDataDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationConcernedDataDictionary();

        $this->assertEquals('registry_violation_concerned_data', $dictionary->getName());
        $this->assertEquals(ViolationConcernedDataDictionary::getConcernedData(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationConcernedDataDictionary::DATA_CIVIL_STATUS                  => 'État civil (nom, sexe, date de naissance, âge...)',
            ViolationConcernedDataDictionary::DATA_SOCIAL_NUMBER                 => 'NIR (Numéro de sécurité sociale)',
            ViolationConcernedDataDictionary::DATA_CONTACT                       => 'Coordonnées (adresse postale ou électronique, numéros de téléphone fixe ou portable...)',
            ViolationConcernedDataDictionary::DATA_IDENTIFICATION_ACCESS         => 'Données d’identification ou d’accès (identifiant, mot de passe, numéro client...)',
            ViolationConcernedDataDictionary::DATA_FINANCIAL                     => 'Données relatives à des informations financières (revenus, numéro de carte de crédit, coordonnées bancaires), économiques',
            ViolationConcernedDataDictionary::DATA_OFFICIAL_DOCUMENT             => 'Documents officiels (Passeports, pièces d’identité, etc.)',
            ViolationConcernedDataDictionary::DATA_LOCATION                      => 'Données de localisation',
            ViolationConcernedDataDictionary::DATA_OFFENSES_CONVICTIONS_SECURITY => 'Données relatives à des infractions, condamnations, mesures de sûreté',
            ViolationConcernedDataDictionary::DATA_UNKNOWN                       => 'Les données concernées ne sont pas connues pour le moment',
            ViolationConcernedDataDictionary::DATA_RACIAL_ETHNIC                 => 'Origine raciale ou ethnique',
            ViolationConcernedDataDictionary::DATA_POLITICAL                     => 'Opinions politiques',
            ViolationConcernedDataDictionary::DATA_PHILOSOPHICAL_RELIGIOUS       => 'Opinions philosophiques ou religieuses',
            ViolationConcernedDataDictionary::DATA_TRADE_UNION_MEMBERSHIP        => 'Appartenance syndicale',
            ViolationConcernedDataDictionary::DATA_SEXUAL_ORIENTATION            => 'Orientation sexuelle',
            ViolationConcernedDataDictionary::DATA_HEALTH                        => 'Données de santé',
            ViolationConcernedDataDictionary::DATA_BIOMETRIC                     => 'Données biométriques',
            ViolationConcernedDataDictionary::DATA_GENETIC                       => 'Données génétiques',
            ViolationConcernedDataDictionary::DATA_OTHER                         => 'La violation concerne d\'autres données',
        ];

        $this->assertEquals($data, ViolationConcernedDataDictionary::getConcernedData());
        $this->assertEquals(
            \array_keys(ViolationConcernedDataDictionary::getConcernedData()),
            ViolationConcernedDataDictionary::getConcernedDataKeys()
        );
    }
}
