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

use App\Domain\Registry\Dictionary\ViolationOriginDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class ViolationOriginDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new ViolationOriginDictionary());
    }

    public function testConstruct()
    {
        $dictionary = new ViolationOriginDictionary();

        $this->assertEquals('registry_violation_origin', $dictionary->getName());
        $this->assertEquals(ViolationOriginDictionary::getOrigins(), $dictionary->getValues());
    }

    public function testDictionary()
    {
        $data = [
            ViolationOriginDictionary::ORIGIN_LOST_STOLEN_EQUIPMENT       => 'Equipement perdu ou volé',
            ViolationOriginDictionary::ORIGIN_LOST_STOLEN_PAPER           => 'Papier perdu, volé ou laissé accessible dans un endroit non sécurisé',
            ViolationOriginDictionary::ORIGIN_LOST_OPENED_MAIL            => 'Courrier perdu ou ouvert avant d\'être retourné à l\'envoyeur',
            ViolationOriginDictionary::ORIGIN_HACK                        => 'Piratage, logiciel malveillant, hameçonnage',
            ViolationOriginDictionary::ORIGIN_TRASH_CONFIDENTIAL_DOCUMENT => 'Mise au rebut de documents papier contenant des données personnelles sans destruction physique',
            ViolationOriginDictionary::ORIGIN_TRASH_CONFIDENTIAL_DEVICE   => 'Mise au rebut d’appareils numériques contenant des données personnelles sans effacement sécurisé',
            ViolationOriginDictionary::ORIGIN_NON_VOLUNTARY_PUBLICATION   => 'Publication non volontaire d\'informations',
            ViolationOriginDictionary::ORIGIN_BAD_PEOPLE_DATA_DISPLAY     => 'Données de la mauvaise personne affichées sur le portail du client',
            ViolationOriginDictionary::ORIGIN_BAD_RECIPIENT_DATA          => 'Données personnelles envoyées à un mauvais destinataire',
            ViolationOriginDictionary::ORIGIN_VERBALLY_DISCLOSED          => 'Informations personnelles divulguées de façon verbale',
        ];

        $this->assertEquals($data, ViolationOriginDictionary::getOrigins());
        $this->assertEquals(
            \array_keys(ViolationOriginDictionary::getOrigins()),
            ViolationOriginDictionary::getOriginsKeys()
        );
    }
}
