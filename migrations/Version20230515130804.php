<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230515130804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE maturity ADD domain_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity ADD CONSTRAINT FK_4636E0E8115F0EE5 FOREIGN KEY (domain_id) REFERENCES maturity_domain (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4636E0E8115F0EE5 ON maturity (domain_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE maturity DROP FOREIGN KEY FK_4636E0E8115F0EE5');
        $this->addSql('DROP INDEX IDX_4636E0E8115F0EE5 ON maturity');
        $this->addSql('ALTER TABLE maturity DROP domain_id');
    }
}
