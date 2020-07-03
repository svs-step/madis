<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200703131955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reporting_log_journal DROP FOREIGN KEY FK_87AE0D7D23EDC87');
        $this->addSql('ALTER TABLE reporting_log_journal ADD CONSTRAINT FK_87AE0D7D23EDC87 FOREIGN KEY (subject_id) REFERENCES loggable_subject (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reporting_log_journal DROP FOREIGN KEY FK_87AE0D7D23EDC87');
        $this->addSql('ALTER TABLE reporting_log_journal ADD CONSTRAINT FK_87AE0D7D23EDC87 FOREIGN KEY (subject_id) REFERENCES loggable_subject (id)');
    }
}
