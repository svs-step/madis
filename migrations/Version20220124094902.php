<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220124094902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE document_categories ADD CONSTRAINT FK_9B30ED3EC33F7837 FOREIGN KEY (document_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE document_categories ADD CONSTRAINT FK_9B30ED3E12469DE2 FOREIGN KEY (category_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE document DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE document_categories DROP FOREIGN KEY FK_9B30ED3EC33F7837');
        $this->addSql('ALTER TABLE document_categories DROP FOREIGN KEY FK_9B30ED3E12469DE2');
    }
}
