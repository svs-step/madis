<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220214104840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_impact DROP is_validated');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE detail detail VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite DROP FOREIGN KEY FK_BF8E57E228B059C');
        $this->addSql('DROP INDEX IDX_BF8E57E228B059C ON aipd_analyse_question_conformite');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite DROP reponse_conformite_id');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace ADD can_be_modified TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental DROP code');
        $this->addSql('ALTER TABLE aipd_modele_mesure_protection CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE detail detail VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_impact ADD is_validated TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection CHANGE nom nom VARCHAR(512) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE detail detail VARCHAR(1024) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite ADD reponse_conformite_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite ADD CONSTRAINT FK_BF8E57E228B059C FOREIGN KEY (reponse_conformite_id) REFERENCES conformite_traitement_reponse (id)');
        $this->addSql('CREATE INDEX IDX_BF8E57E228B059C ON aipd_analyse_question_conformite (reponse_conformite_id)');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace DROP can_be_modified');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental ADD code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE aipd_modele_mesure_protection CHANGE nom nom VARCHAR(512) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE detail detail VARCHAR(1024) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
