<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219091346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase length of several fields to 500 characters';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_access_control_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_tracability_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_saving_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_update_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_other_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY archival_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY encrypted_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY access_control_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY updating_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY backup_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY deletion_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY tracking_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY has_comment_comment VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY other_comment VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_access_control_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_tracability_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_saving_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_update_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment MODIFY security_other_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY archival_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY encrypted_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY access_control_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY updating_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY backup_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY deletion_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY tracking_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY has_comment_comment VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_tool MODIFY other_comment VARCHAR(255) DEFAULT NULL');
    }
}
