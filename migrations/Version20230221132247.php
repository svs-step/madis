<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221132247 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment ADD updated_by LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_violation ADD updated_by LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_request ADD updated_by LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_mesurement ADD updated_by LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_contractor ADD updated_by LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment DROP updated_by');
        $this->addSql('ALTER TABLE registry_violation DROP updated_by');
        $this->addSql('ALTER TABLE registry_request DROP updated_by');
        $this->addSql('ALTER TABLE registry_mesurement DROP updated_by');
        $this->addSql('ALTER TABLE registry_contractor DROP updated_by');
    }
}
