<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220224125223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE treatment_violation (violation_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', treatment_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_F59F07FA7386118A (violation_id), INDEX IDX_F59F07FA471C0366 (treatment_id), PRIMARY KEY(violation_id, treatment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE treatment_violation ADD CONSTRAINT FK_F59F07FA7386118A FOREIGN KEY (violation_id) REFERENCES registry_violation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE treatment_violation ADD CONSTRAINT FK_F59F07FA471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE treatment_request DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE treatment_request ADD PRIMARY KEY (request_id, treatment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE treatment_violation');
        $this->addSql('ALTER TABLE treatment_request DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE treatment_request ADD PRIMARY KEY (treatment_id, request_id)');
    }
}
