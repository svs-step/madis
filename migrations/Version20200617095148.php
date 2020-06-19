<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200617095148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation DROP FOREIGN KEY FK_9D0C7E679E6B1585');
        $this->addSql('DROP INDEX IDX_9D0C7E679E6B1585 ON registry_conformite_organisation_evaluation');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation CHANGE organisation_id collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation ADD CONSTRAINT FK_9D0C7E67BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('CREATE INDEX IDX_9D0C7E67BD56F776 ON registry_conformite_organisation_evaluation (collectivity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation DROP FOREIGN KEY FK_9D0C7E67BD56F776');
        $this->addSql('DROP INDEX IDX_9D0C7E67BD56F776 ON registry_conformite_organisation_evaluation');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation CHANGE collectivity_id organisation_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation ADD CONSTRAINT FK_9D0C7E679E6B1585 FOREIGN KEY (organisation_id) REFERENCES user_collectivity (id)');
        $this->addSql('CREATE INDEX IDX_9D0C7E679E6B1585 ON registry_conformite_organisation_evaluation (organisation_id)');
    }
}
