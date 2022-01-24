<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220124134301 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_favorite_documents (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', document_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_C4E401E1A76ED395 (user_id), INDEX IDX_C4E401E1C33F7837 (document_id), PRIMARY KEY(user_id, document_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_favorite_documents ADD CONSTRAINT FK_C4E401E1A76ED395 FOREIGN KEY (user_id) REFERENCES user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_favorite_documents ADD CONSTRAINT FK_C4E401E1C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_favorite_documents');
    }
}
