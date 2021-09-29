<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210930122129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_impact ADD conformite_traitement_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD statut VARCHAR(255) NOT NULL, ADD modele_analyse VARCHAR(255) NOT NULL, ADD date_validation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB024CEF983B6 FOREIGN KEY (conformite_traitement_id) REFERENCES conformite_traitement (id)');
        $this->addSql('CREATE INDEX IDX_DDAAB024CEF983B6 ON aipd_analyse_impact (conformite_traitement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB024CEF983B6');
        $this->addSql('DROP INDEX IDX_DDAAB024CEF983B6 ON aipd_analyse_impact');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP conformite_traitement_id, DROP statut, DROP modele_analyse, DROP date_validation, DROP created_at, DROP updated_at');
    }
}
