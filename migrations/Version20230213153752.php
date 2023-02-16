<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213153752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE registry_tool ADD collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_tool ADD CONSTRAINT FK_3C2A7CF2BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_3C2A7CF2BD56F776 ON registry_tool (collectivity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE registry_tool DROP FOREIGN KEY FK_3C2A7CF2BD56F776');
        $this->addSql('DROP INDEX IDX_3C2A7CF2BD56F776 ON registry_tool');
        $this->addSql('ALTER TABLE registry_tool DROP collectivity_id');
    }
}
