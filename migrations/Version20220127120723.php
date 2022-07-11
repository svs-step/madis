<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220127120723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document ADD is_link TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE document_categories DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE document_categories ADD PRIMARY KEY (category_id, document_id)');
        $this->addSql('ALTER TABLE user_favorite_documents DROP FOREIGN KEY FK_C4E401E1A76ED395');
        $this->addSql('ALTER TABLE user_favorite_documents DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_favorite_documents ADD CONSTRAINT FK_C4E401E1A76ED395 FOREIGN KEY (user_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE user_favorite_documents ADD PRIMARY KEY (document_id, user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document DROP is_link');
        $this->addSql('ALTER TABLE document_categories DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE document_categories ADD PRIMARY KEY (document_id, category_id)');
        $this->addSql('ALTER TABLE user_favorite_documents DROP FOREIGN KEY FK_C4E401E1A76ED395');
        $this->addSql('ALTER TABLE user_favorite_documents DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE user_favorite_documents ADD CONSTRAINT FK_C4E401E1A76ED395 FOREIGN KEY (user_id) REFERENCES user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_favorite_documents ADD PRIMARY KEY (user_id, document_id)');
    }
}
