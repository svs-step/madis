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
final class Version20180629090158 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment ADD security_access_control_check TINYINT(1) NOT NULL, ADD security_access_control_comment VARCHAR(255) DEFAULT NULL, ADD security_tracability_check TINYINT(1) NOT NULL, ADD security_tracability_comment VARCHAR(255) DEFAULT NULL, ADD security_saving_check TINYINT(1) NOT NULL, ADD security_saving_comment VARCHAR(255) DEFAULT NULL, ADD security_encryptioncheck TINYINT(1) NOT NULL, ADD security_encryptioncomment VARCHAR(255) DEFAULT NULL, ADD security_update_check TINYINT(1) NOT NULL, ADD security_update_comment VARCHAR(255) DEFAULT NULL, ADD security_other_check TINYINT(1) NOT NULL, ADD security_other_comment VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment DROP security_access_control_check, DROP security_access_control_comment, DROP security_tracability_check, DROP security_tracability_comment, DROP security_saving_check, DROP security_saving_comment, DROP security_encryptioncheck, DROP security_encryptioncomment, DROP security_update_check, DROP security_update_comment, DROP security_other_check, DROP security_other_comment');
    }
}
