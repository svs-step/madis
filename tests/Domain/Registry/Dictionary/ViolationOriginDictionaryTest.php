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
