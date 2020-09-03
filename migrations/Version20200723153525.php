<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200723153525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reporting_log_journal (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', action VARCHAR(255) NOT NULL, user_full_name VARCHAR(255) NOT NULL, user_email VARCHAR(255) NOT NULL, subject_type VARCHAR(255) NOT NULL, subject_id VARCHAR(255) NOT NULL, subject_name VARCHAR(255) NOT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_87AE0D7DBD56F776 (collectivity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reporting_log_journal ADD CONSTRAINT FK_87AE0D7DBD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE reporting_log_journal');
    }
}
