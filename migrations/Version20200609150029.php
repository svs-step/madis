<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200609150029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation ADD organisation_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', DROP pilote');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation ADD CONSTRAINT FK_9D0C7E679E6B1585 FOREIGN KEY (organisation_id) REFERENCES user_collectivity (id)');
        $this->addSql('CREATE INDEX IDX_9D0C7E679E6B1585 ON registry_conformite_organisation_evaluation (organisation_id)');
        $this->addSql('ALTER TABLE registry_conformite_organisation_conformite ADD evaluation_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD pilote VARCHAR(255) DEFAULT NULL, CHANGE conformite conformite DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_conformite_organisation_conformite ADD CONSTRAINT FK_7BD97140456C5646 FOREIGN KEY (evaluation_id) REFERENCES registry_conformite_organisation_evaluation (id)');
        $this->addSql('CREATE INDEX IDX_7BD97140456C5646 ON registry_conformite_organisation_conformite (evaluation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_conformite_organisation_conformite DROP FOREIGN KEY FK_7BD97140456C5646');
        $this->addSql('DROP INDEX IDX_7BD97140456C5646 ON registry_conformite_organisation_conformite');
        $this->addSql('ALTER TABLE registry_conformite_organisation_conformite DROP evaluation_id, DROP pilote, CHANGE conformite conformite INT DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation DROP FOREIGN KEY FK_9D0C7E679E6B1585');
        $this->addSql('DROP INDEX IDX_9D0C7E679E6B1585 ON registry_conformite_organisation_evaluation');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation ADD pilote VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP organisation_id');
    }
}
