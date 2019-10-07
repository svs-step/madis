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
final class Version20190308151033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $existingRows = $this->connection->query("SELECT code FROM registry_treatment_data_category WHERE code = 'civility'")->rowCount();

        // REPLACE 'civility' BY 'name', 'birth', 'professional-situation'
        // Don't transform civility if not exists in database
        if (0 === $existingRows) {
            return;
        }

        $data = [
            [
                'code'     => 'name',
                'name'     => 'Nom, Prénom',
                'position' => 1,
                'sensible' => 0,
            ],
            [
                'code'     => 'birth',
                'name'     => 'Date, lieu de naissance',
                'position' => 2,
                'sensible' => 0,
            ],
            [
                'code'     => 'professional-situation',
                'name'     => 'Situation professionnelle',
                'position' => 11,
                'sensible' => 0,
            ],
        ];
        foreach ($data as $item) {
            $this->addSql("INSERT INTO registry_treatment_data_category(code, name, position, sensible) VALUES ('{$item['code']}', '{$item['name']}', {$item['position']}, {$item['sensible']})");
        }

        $rowsToUpdate = $this->connection->query("SELECT treatment_id FROM registry_assoc_treatment_data_category WHERE treatment_data_category_code = 'civility'")->fetchAll();
        foreach ($rowsToUpdate as $row) {
            $this->addSql("INSERT INTO registry_assoc_treatment_data_category VALUES ('{$row['treatment_id']}', 'name'), ('{$row['treatment_id']}', 'birth'), ('{$row['treatment_id']}', 'professional-situation')");
            $this->addSql("DELETE FROM registry_assoc_treatment_data_category WHERE treatment_id = '{$row['treatment_id']}' AND treatment_data_category_code = 'civility'");
        }

        $this->addSql("DELETE FROM registry_treatment_data_category WHERE code = 'civility'");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $existingRows = $this->connection->query("SELECT code FROM registry_treatment_data_category WHERE code in ('name', 'birth', 'professional-situation')")->rowCount();

        // REPLACE 'name', 'birth', 'professional-situation' BY 'civility'
        // Don't transform to civility if their replacement values don't exist in database
        if (0 === $existingRows) {
            return;
        }

        $this->addSql("INSERT INTO registry_treatment_data_category(code, name, position, sensible) VALUES ('civility', 'État civil', 1, 0)");

        $rowsToUpdate = $this->connection->query("SELECT DISTINCT treatment_id FROM registry_assoc_treatment_data_category WHERE treatment_data_category_code in ('name', 'birth', 'professional-situation')")->fetchAll();
        foreach ($rowsToUpdate as $row) {
            $this->addSql("INSERT INTO registry_assoc_treatment_data_category VALUES ('{$row['treatment_id']}', 'civility')");
            $this->addSql("DELETE FROM registry_assoc_treatment_data_category WHERE treatment_id = '{$row['treatment_id']}' AND treatment_data_category_code in ('name', 'birth', 'professional-situation')");
        }

        $this->addSql("DELETE FROM registry_treatment_data_category WHERE code in ('name', 'birth', 'professional-situation')");
    }
}
