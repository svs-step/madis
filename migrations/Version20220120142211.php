<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220120142211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conformite_traitement_reponse ADD analyse_question_conformite_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE conformite_traitement_reponse ADD CONSTRAINT FK_6B4E420F34B82B6 FOREIGN KEY (analyse_question_conformite_id) REFERENCES aipd_analyse_question_conformite (id)');
        $this->addSql('CREATE INDEX IDX_6B4E420F34B82B6 ON conformite_traitement_reponse (analyse_question_conformite_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conformite_traitement_reponse DROP FOREIGN KEY FK_6B4E420F34B82B6');
        $this->addSql('DROP INDEX IDX_6B4E420F34B82B6 ON conformite_traitement_reponse');
        $this->addSql('ALTER TABLE conformite_traitement_reponse DROP analyse_question_conformite_id');
    }
}
