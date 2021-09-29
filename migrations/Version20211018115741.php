<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018115741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_analyse_impact (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', conformite_traitement_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', statut VARCHAR(255) NOT NULL, modele_analyse VARCHAR(255) NOT NULL, date_validation DATE DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DDAAB024CEF983B6 (conformite_traitement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_analyse_mesure_protection (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, nom_court VARCHAR(255) NOT NULL, label_livrable VARCHAR(255) NOT NULL, phrase_preconisation VARCHAR(255) NOT NULL, detail VARCHAR(255) NOT NULL, poids_vraisemblance INT NOT NULL, poids_gravite INT NOT NULL, reponse VARCHAR(255) DEFAULT NULL, INDEX IDX_10DB7C5C498AFC31 (analyse_impact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_analyse_question_conformite (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', question VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, is_justification_obligatoire TINYINT(1) NOT NULL, texte_conformite VARCHAR(255) DEFAULT NULL, texte_non_conformite_majeure VARCHAR(255) DEFAULT NULL, texte_non_conformite_mineure VARCHAR(255) DEFAULT NULL, justificatif VARCHAR(510) DEFAULT NULL, INDEX IDX_BF8E57E498AFC31 (analyse_impact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_analyse_scenario_menace (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, is_visible TINYINT(1) NOT NULL, is_disponibilite TINYINT(1) NOT NULL, is_integreite TINYINT(1) NOT NULL, is_confidentialite TINYINT(1) NOT NULL, vraisemblance VARCHAR(255) NOT NULL, gravite VARCHAR(255) NOT NULL, precisions VARCHAR(255) DEFAULT NULL, INDEX IDX_EC6B120B498AFC31 (analyse_impact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_modele_scenario_menace_mesure_protection (modele_scenario_menace_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesure_protection_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_D482719194A6586B (modele_scenario_menace_id), INDEX IDX_D4827191404E9273 (mesure_protection_id), PRIMARY KEY(modele_scenario_menace_id, mesure_protection_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB024CEF983B6 FOREIGN KEY (conformite_traitement_id) REFERENCES conformite_traitement (id)');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection ADD CONSTRAINT FK_10DB7C5C498AFC31 FOREIGN KEY (analyse_impact_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite ADD CONSTRAINT FK_BF8E57E498AFC31 FOREIGN KEY (analyse_impact_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace ADD CONSTRAINT FK_EC6B120B498AFC31 FOREIGN KEY (analyse_impact_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D482719194A6586B FOREIGN KEY (modele_scenario_menace_id) REFERENCES aipd_modele_scenario_menace (id)');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D4827191404E9273 FOREIGN KEY (mesure_protection_id) REFERENCES aipd_mesure_protection (id)');
        $this->addSql('DROP TABLE aipd_scenario_menace_mesure_protection');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental ADD analyse_impact_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental ADD CONSTRAINT FK_332F007498AFC31 FOREIGN KEY (analyse_impact_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('CREATE INDEX IDX_332F007498AFC31 ON aipd_critere_principe_fondamental (analyse_impact_id)');
        $this->addSql('ALTER TABLE aipd_modele_question_conformite ADD position VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection DROP FOREIGN KEY FK_10DB7C5C498AFC31');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite DROP FOREIGN KEY FK_BF8E57E498AFC31');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace DROP FOREIGN KEY FK_EC6B120B498AFC31');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental DROP FOREIGN KEY FK_332F007498AFC31');
        $this->addSql('CREATE TABLE aipd_scenario_menace_mesure_protection (abstract_scenario_menace_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', mesure_protection_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', INDEX IDX_51BC485EDA8234FB (abstract_scenario_menace_id), INDEX IDX_51BC485E404E9273 (mesure_protection_id), PRIMARY KEY(abstract_scenario_menace_id, mesure_protection_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE aipd_scenario_menace_mesure_protection ADD CONSTRAINT FK_51BC485E404E9273 FOREIGN KEY (mesure_protection_id) REFERENCES aipd_mesure_protection (id)');
        $this->addSql('ALTER TABLE aipd_scenario_menace_mesure_protection ADD CONSTRAINT FK_51BC485EDA8234FB FOREIGN KEY (abstract_scenario_menace_id) REFERENCES aipd_modele_scenario_menace (id)');
        $this->addSql('DROP TABLE aipd_analyse_impact');
        $this->addSql('DROP TABLE aipd_analyse_mesure_protection');
        $this->addSql('DROP TABLE aipd_analyse_question_conformite');
        $this->addSql('DROP TABLE aipd_analyse_scenario_menace');
        $this->addSql('DROP TABLE aipd_modele_scenario_menace_mesure_protection');
        $this->addSql('DROP INDEX IDX_332F007498AFC31 ON aipd_critere_principe_fondamental');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental DROP analyse_impact_id');
        $this->addSql('ALTER TABLE aipd_modele_question_conformite DROP position');
    }
}
