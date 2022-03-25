<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210927144037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_critere_principe_fondamental (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', modele_analyse_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', label VARCHAR(255) NOT NULL, label_livrable VARCHAR(255) NOT NULL, reponse VARCHAR(255) NOT NULL, visible TINYINT(1) NOT NULL, texte_conformite VARCHAR(255) NOT NULL, texte_non_conformite VARCHAR(255) NOT NULL, non_applicable VARCHAR(255) NOT NULL, justification VARCHAR(255) NOT NULL, INDEX IDX_332F007EDA9114E (modele_analyse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_mesure_protection (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, nom_court VARCHAR(255) NOT NULL, label_livrable VARCHAR(255) NOT NULL, phrase_preconisation VARCHAR(255) NOT NULL, detail VARCHAR(255) NOT NULL, poids_vraisemblance INT NOT NULL, poids_gravite INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_modele (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', label_amelioration_prevue VARCHAR(255) NOT NULL, label_insatisfait VARCHAR(255) NOT NULL, label_satisfaisant VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_modele_question_conformite (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', modele_analyse_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', question VARCHAR(255) NOT NULL, is_justification_obligatoire TINYINT(1) NOT NULL, texte_conformite VARCHAR(255) DEFAULT NULL, texte_non_conformite_majeure VARCHAR(255) DEFAULT NULL, texte_non_conformite_mineure VARCHAR(255) DEFAULT NULL, INDEX IDX_BA32E44AEDA9114E (modele_analyse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_modele_scenario_menace (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', modele_analyse_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, is_visible TINYINT(1) NOT NULL, is_disponibilite TINYINT(1) NOT NULL, is_integreite TINYINT(1) NOT NULL, is_confidentialite TINYINT(1) NOT NULL, vraisemblance VARCHAR(255) NOT NULL, gravite VARCHAR(255) NOT NULL, precisions VARCHAR(255) NOT NULL, INDEX IDX_EC2B4EE6EDA9114E (modele_analyse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_scenario_menace_mesure_protection (abstract_scenario_menace_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesure_protection_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_51BC485EDA8234FB (abstract_scenario_menace_id), INDEX IDX_51BC485E404E9273 (mesure_protection_id), PRIMARY KEY(abstract_scenario_menace_id, mesure_protection_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental ADD CONSTRAINT FK_332F007EDA9114E FOREIGN KEY (modele_analyse_id) REFERENCES aipd_modele (id)');
        $this->addSql('ALTER TABLE aipd_modele_question_conformite ADD CONSTRAINT FK_BA32E44AEDA9114E FOREIGN KEY (modele_analyse_id) REFERENCES aipd_modele (id)');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace ADD CONSTRAINT FK_EC2B4EE6EDA9114E FOREIGN KEY (modele_analyse_id) REFERENCES aipd_modele (id)');
        $this->addSql('ALTER TABLE aipd_scenario_menace_mesure_protection ADD CONSTRAINT FK_51BC485EDA8234FB FOREIGN KEY (abstract_scenario_menace_id) REFERENCES aipd_modele_scenario_menace (id)');
        $this->addSql('ALTER TABLE aipd_scenario_menace_mesure_protection ADD CONSTRAINT FK_51BC485E404E9273 FOREIGN KEY (mesure_protection_id) REFERENCES aipd_mesure_protection (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_scenario_menace_mesure_protection DROP FOREIGN KEY FK_51BC485E404E9273');
        $this->addSql('ALTER TABLE aipd_critere_principe_fondamental DROP FOREIGN KEY FK_332F007EDA9114E');
        $this->addSql('ALTER TABLE aipd_modele_question_conformite DROP FOREIGN KEY FK_BA32E44AEDA9114E');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace DROP FOREIGN KEY FK_EC2B4EE6EDA9114E');
        $this->addSql('ALTER TABLE aipd_scenario_menace_mesure_protection DROP FOREIGN KEY FK_51BC485EDA8234FB');
        $this->addSql('DROP TABLE aipd_critere_principe_fondamental');
        $this->addSql('DROP TABLE aipd_mesure_protection');
        $this->addSql('DROP TABLE aipd_modele');
        $this->addSql('DROP TABLE aipd_modele_question_conformite');
        $this->addSql('DROP TABLE aipd_modele_scenario_menace');
        $this->addSql('DROP TABLE aipd_scenario_menace_mesure_protection');
    }
}
