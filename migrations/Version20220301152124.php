<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220301152124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification ADD collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD read_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD created_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD module VARCHAR(255) NOT NULL, ADD object JSON NOT NULL COMMENT \'(DC2Type:json_array)\', ADD read_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CABD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF5675CD0 FOREIGN KEY (read_by_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_BF5476CABD56F776 ON notification (collectivity_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAF5675CD0 ON notification (read_by_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAB03A8386 ON notification (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CABD56F776');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAF5675CD0');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAB03A8386');
        $this->addSql('DROP INDEX IDX_BF5476CABD56F776 ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CAF5675CD0 ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CAB03A8386 ON notification');
        $this->addSql('ALTER TABLE notification DROP collectivity_id, DROP read_by_id, DROP created_by_id, DROP module, DROP object, DROP read_at');
    }
}
