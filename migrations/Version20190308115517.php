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

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190308115517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_treatment_data_category (code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, position INT NOT NULL, sensible TINYINT(1) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_assoc_treatment_data_category (treatment_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', treatment_data_category_code VARCHAR(50) NOT NULL, INDEX IDX_DB9E371A471C0366 (treatment_id), INDEX IDX_DB9E371A6E5DCE39 (treatment_data_category_code), PRIMARY KEY(treatment_id, treatment_data_category_code)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_assoc_treatment_data_category ADD CONSTRAINT FK_DB9E371A471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id)');
        $this->addSql('ALTER TABLE registry_assoc_treatment_data_category ADD CONSTRAINT FK_DB9E371A6E5DCE39 FOREIGN KEY (treatment_data_category_code) REFERENCES registry_treatment_data_category (code)');

        $isDataCategoriesInitialized = false;

        $treatmentResult = $this->connection->query('SELECT id, data_category from registry_treatment')->fetchAll();
        foreach ($treatmentResult as $treatment) {
            $categories = \json_decode($treatment['data_category'], true);

            foreach ($categories as $categoryItem) {
                if (false === $isDataCategoriesInitialized) {
                    // Only initialize data if needed for migration (this let eg. fixtures enabling for dev mode)
                    $this->initializeDataCategories();
                    $isDataCategoriesInitialized = true;
                }
                $this->addSql("INSERT INTO registry_assoc_treatment_data_category(treatment_id, treatment_data_category_code) VALUES ('{$treatment['id']}', '{$categoryItem}')");
            }
        }

        $this->addSql('ALTER TABLE registry_treatment DROP data_category');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment ADD data_category JSON NOT NULL COMMENT \'(DC2Type:json_array)\'');

        $treatmentResult = $this->connection->query('SELECT id from registry_treatment')->fetchAll();
        foreach ($treatmentResult as $treatment) {
            $dataCategories = $this->connection->query("SELECT treatment_data_category_code FROM registry_assoc_treatment_data_category WHERE treatment_id='{$treatment['id']}'")->fetchAll();
            $codes          = [];
            foreach ($dataCategories as $category) {
                $codes[] = $category['treatment_data_category_code'];
            }
            $encodedCodes = \json_encode($codes);
            $this->addSql("UPDATE registry_treatment SET data_category = '$encodedCodes' WHERE id = '{$treatment['id']}'");
        }

        $this->addSql('DROP TABLE registry_assoc_treatment_data_category');
        $this->addSql('DROP TABLE registry_treatment_data_category');
    }

    protected function initializeDataCategories(): void
    {
        $data = [
            [
                'code'     => 'civility',
                'name'     => 'État civil',
                'position' => 1,
                'sensible' => 0,
            ],
            // Split Etat civil
            [
                'code'     => 'family-situation',
                'name'     => 'Situation familiale',
                'position' => 3,
                'sensible' => 0,
            ],
            [
                'code'     => 'filiation',
                'name'     => 'Filiation',
                'position' => 4,
                'sensible' => 0,
            ],
            [
                'code'     => 'postal',
                'name'     => 'Coordonnées postales',
                'position' => 5,
                'sensible' => 0,
            ],
            [
                'code'     => 'phone',
                'name'     => 'Coordonnées téléphoniques',
                'position' => 6,
                'sensible' => 0,
            ],
            [
                'code'     => 'bank',
                'name'     => 'Information bancaire',
                'position' => 7,
                'sensible' => 0,
            ],
            [
                'code'     => 'bank-situation',
                'name'     => 'Situation bancaire',
                'position' => 8,
                'sensible' => 0,
            ],
            [
                'code'     => 'heritage',
                'name'     => 'Patrimoine',
                'position' => 9,
                'sensible' => 0,
            ],
            [
                'code'     => 'tax-situation',
                'name'     => 'Situation fiscale',
                'position' => 10,
                'sensible' => 0,
            ],
            // Situation pro
            [
                'code'     => 'earning',
                'name'     => 'Revenus',
                'position' => 12,
                'sensible' => 0,
            ],
            [
                'code'     => 'email',
                'name'     => 'Emails',
                'position' => 13,
                'sensible' => 0,
            ],
            [
                'code'     => 'ip',
                'name'     => 'Adresse IP',
                'position' => 14,
                'sensible' => 0,
            ],
            [
                'code'     => 'connexion',
                'name'     => 'Connexion',
                'position' => 15,
                'sensible' => 0,
            ],
            [
                'code'     => 'geo',
                'name'     => 'Géolocalisation',
                'position' => 16,
                'sensible' => 0,
            ],
            [
                'code'     => 'caf',
                'name'     => 'Numéro de CAF',
                'position' => 17,
                'sensible' => 0,
            ],
            [
                'code'     => 'health',
                'name'     => 'Santé',
                'position' => 18,
                'sensible' => 1,
            ],
            [
                'code'     => 'social-security-number',
                'name'     => 'Numéro de Sécurité Sociale',
                'position' => 19,
                'sensible' => 1,
            ],
            [
                'code'     => 'identity',
                'name'     => 'Pièces d’identité',
                'position' => 20,
                'sensible' => 0,
            ],
            [
                'code'     => 'picture',
                'name'     => 'Photos-vidéos',
                'position' => 21,
                'sensible' => 0,
            ],
            [
                'code'     => 'syndicate',
                'name'     => 'Appartenance Syndicale',
                'position' => 22,
                'sensible' => 1,
            ],
            [
                'code'     => 'public-religious-opinion',
                'name'     => 'Opinion politique ou religieuse',
                'position' => 23,
                'sensible' => 1,
            ],
            [
                'code'     => 'racial-ethnic-opinion',
                'name'     => 'Origine raciale ou ethnique',
                'position' => 24,
                'sensible' => 1,
            ],
            [
                'code'     => 'sex-life',
                'name'     => 'Vie sexuelle',
                'position' => 25,
                'sensible' => 1,
            ],
        ];

        foreach ($data as $item) {
            $this->addSql("INSERT INTO registry_treatment_data_category(code, name, position, sensible) VALUES ('{$item['code']}', '{$item['name']}', {$item['position']}, {$item['sensible']})");
        }
    }
}
