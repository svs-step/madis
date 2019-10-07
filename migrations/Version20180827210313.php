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
final class Version20180827210313 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_domain ADD color VARCHAR(20) NOT NULL');

        $this->addSql('UPDATE maturity_domain set color="info" where name="Vie privée"');
        $this->addSql('UPDATE maturity_domain set color="success" where name="Organisation"');
        $this->addSql('UPDATE maturity_domain set color="warning" where name="Juridique"');
        $this->addSql('UPDATE maturity_domain set color="primary" where name="Violation de données"');
        $this->addSql('UPDATE maturity_domain set color="danger" where name="Technique"');
        $this->addSql('UPDATE maturity_domain set color="info" where name="Sensibilisation Formation"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_domain DROP color');
    }
}
