<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240209102637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Increase length of several fields to 1000 characters';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_modele_question_conformite MODIFY texte_conformite VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_modele_question_conformite MODIFY texte_non_conformite_majeure VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_modele_question_conformite MODIFY texte_non_conformite_mineure VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace MODIFY nom VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace MODIFY precisions VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental MODIFY texte_conformite VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental MODIFY texte_non_conformite VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental MODIFY non_applicable VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental MODIFY justification VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_avis MODIFY detail VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace MODIFY nom VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace MODIFY precisions VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite MODIFY texte_conformite VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite MODIFY texte_non_conformite_majeure VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite MODIFY texte_non_conformite_mineure VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite MODIFY justificatif VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment_shelf_life MODIFY duration VARCHAR(500) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_modele_question_conformite MODIFY texte_conformite VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_modele_question_conformite MODIFY texte_non_conformite_majeure VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_modele_question_conformite MODIFY texte_non_conformite_mineure VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace MODIFY nom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace MODIFY precisions VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental MODIFY texte_conformite VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental MODIFY texte_non_conformite VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental MODIFY non_applicable VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental MODIFY justification VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_avis MODIFY detail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace MODIFY nom VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace MODIFY precisions VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite MODIFY texte_conformite VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite MODIFY texte_non_conformite_majeure VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite MODIFY texte_non_conformite_mineure VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite MODIFY justificatif VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_treatment_shelf_life MODIFY duration VARCHAR(255) DEFAULT NULL');
    }
}
