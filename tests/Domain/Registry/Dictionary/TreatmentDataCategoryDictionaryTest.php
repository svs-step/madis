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

use App\Domain\Registry\Dictionary\TreatmentDataCategoryDictionary;
use Knp\DictionaryBundle\Dictionary\SimpleDictionary;
use PHPUnit\Framework\TestCase;

class TreatmentDataCategoryDictionaryTest extends TestCase
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(SimpleDictionary::class, new TreatmentDataCategoryDictionary());
    }

    public function testConstruct()
    {
        $roleDictionary = new TreatmentDataCategoryDictionary();

        $this->assertEquals('registry_treatment_data_category', $roleDictionary->getName());
        $this->assertEquals(TreatmentDataCategoryDictionary::getCategories(), $roleDictionary->getValues());
    }

    public function testGetRoles()
    {
        $data = [
            TreatmentDataCategoryDictionary::CATEGORY_CIVILITY                 => 'Etat civil',
            TreatmentDataCategoryDictionary::CATEGORY_POSTAL                   => 'Coordonnées postales',
            TreatmentDataCategoryDictionary::CATEGORY_PHONE                    => 'Coordonnées téléphoniques',
            TreatmentDataCategoryDictionary::CATEGORY_EMAIL                    => 'Emails',
            TreatmentDataCategoryDictionary::CATEGORY_IP                       => 'Adresse IP',
            TreatmentDataCategoryDictionary::CATEGORY_GEO                      => 'Géolocalisation',
            TreatmentDataCategoryDictionary::CATEGORY_PICTURE                  => 'Photos-vidéos',
            TreatmentDataCategoryDictionary::CATEGORY_FAMILY_SITUATION         => 'Situation familiale',
            TreatmentDataCategoryDictionary::CATEGORY_FILIATION                => 'Filiation',
            TreatmentDataCategoryDictionary::CATEGORY_HEALTH                   => 'Santé',
            TreatmentDataCategoryDictionary::CATEGORY_CHILD                    => 'Enfant de moins de 16 ans',
            TreatmentDataCategoryDictionary::CATEGORY_SOCIAL_NUM               => 'Numéro de Sécurité Social',
            TreatmentDataCategoryDictionary::CATEGORY_CAF_NUM                  => 'Numéro de CAF',
            TreatmentDataCategoryDictionary::CATEGORY_SYNDICATE                => 'Appartenance Syndicale',
            TreatmentDataCategoryDictionary::CATEGORY_CONNEXION                => 'Connexion',
            TreatmentDataCategoryDictionary::CATEGORY_BANK                     => 'Information bancaire',
            TreatmentDataCategoryDictionary::CATEGORY_HERITAGE                 => 'Patrimoine',
            TreatmentDataCategoryDictionary::CATEGORY_EARNING                  => 'Revenus',
            TreatmentDataCategoryDictionary::CATEGORY_TAX_SITUATION            => 'Situation fiscale',
            TreatmentDataCategoryDictionary::CATEGORY_BANK_SITUATION           => 'Situation bancaire',
            TreatmentDataCategoryDictionary::CATEGORY_IDENTITY                 => 'Pièces d’identité',
            TreatmentDataCategoryDictionary::CATEGORY_PUBLIC_RELIGIOUS_OPINION => 'Opinion politique ou religieuse',
            TreatmentDataCategoryDictionary::CATEGORY_RACIAL_ETHNIC_OPINION    => 'Origine raciale ou ethnique',
            TreatmentDataCategoryDictionary::CATEGORY_SEX_LIFE                 => 'Vie sexuelle',
        ];

        $this->assertEquals($data, TreatmentDataCategoryDictionary::getCategories());
    }

    public function testGetRolesKeys()
    {
        $data = [
            TreatmentDataCategoryDictionary::CATEGORY_CIVILITY,
            TreatmentDataCategoryDictionary::CATEGORY_POSTAL,
            TreatmentDataCategoryDictionary::CATEGORY_PHONE,
            TreatmentDataCategoryDictionary::CATEGORY_EMAIL,
            TreatmentDataCategoryDictionary::CATEGORY_IP,
            TreatmentDataCategoryDictionary::CATEGORY_GEO,
            TreatmentDataCategoryDictionary::CATEGORY_PICTURE,
            TreatmentDataCategoryDictionary::CATEGORY_FAMILY_SITUATION,
            TreatmentDataCategoryDictionary::CATEGORY_FILIATION,
            TreatmentDataCategoryDictionary::CATEGORY_HEALTH,
            TreatmentDataCategoryDictionary::CATEGORY_CHILD,
            TreatmentDataCategoryDictionary::CATEGORY_SOCIAL_NUM,
            TreatmentDataCategoryDictionary::CATEGORY_CAF_NUM,
            TreatmentDataCategoryDictionary::CATEGORY_SYNDICATE,
            TreatmentDataCategoryDictionary::CATEGORY_CONNEXION,
            TreatmentDataCategoryDictionary::CATEGORY_BANK,
            TreatmentDataCategoryDictionary::CATEGORY_HERITAGE,
            TreatmentDataCategoryDictionary::CATEGORY_EARNING,
            TreatmentDataCategoryDictionary::CATEGORY_TAX_SITUATION,
            TreatmentDataCategoryDictionary::CATEGORY_BANK_SITUATION,
            TreatmentDataCategoryDictionary::CATEGORY_IDENTITY,
            TreatmentDataCategoryDictionary::CATEGORY_PUBLIC_RELIGIOUS_OPINION,
            TreatmentDataCategoryDictionary::CATEGORY_RACIAL_ETHNIC_OPINION,
            TreatmentDataCategoryDictionary::CATEGORY_SEX_LIFE,
        ];

        $this->assertEquals($data, TreatmentDataCategoryDictionary::getCategoryKeys());
    }
}
