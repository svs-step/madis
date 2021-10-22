<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211020142034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET foreign_key_checks = 0');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection DROP FOREIGN KEY FK_D4827191404E9273');
        $this->addSql('CREATE TABLE aipd_modele_mesure_protection (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, nom_court VARCHAR(255) NOT NULL, label_livrable VARCHAR(255) NOT NULL, phrase_preconisation VARCHAR(255) NOT NULL, detail VARCHAR(255) NOT NULL, poids_vraisemblance INT NOT NULL, poids_gravite INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE aipd_mesure_protection');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection ADD scenario_menace_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection ADD CONSTRAINT FK_10DB7C5C1B01FC3D FOREIGN KEY (scenario_menace_id) REFERENCES aipd_analyse_scenario_menace (id)');
        $this->addSql('CREATE INDEX IDX_10DB7C5C1B01FC3D ON aipd_analyse_mesure_protection (scenario_menace_id)');
        $this->addSql('DROP INDEX IDX_D4827191404E9273 ON aipd_modele_scenario_menace_mesure_protection');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection CHANGE mesure_protection_id modele_mesure_protection_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D48271917D1480F7 FOREIGN KEY (modele_mesure_protection_id) REFERENCES aipd_modele_mesure_protection (id)');
        $this->addSql('CREATE INDEX IDX_D48271917D1480F7 ON aipd_modele_scenario_menace_mesure_protection (modele_mesure_protection_id)');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD PRIMARY KEY (modele_scenario_menace_id, modele_mesure_protection_id)');
        $this->addSql('SET foreign_key_checks = 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection DROP FOREIGN KEY FK_D48271917D1480F7');
        $this->addSql('CREATE TABLE aipd_mesure_protection (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom_court VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label_livrable VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phrase_preconisation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, detail VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, poids_vraisemblance INT NOT NULL, poids_gravite INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE aipd_modele_mesure_protection');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection DROP FOREIGN KEY FK_10DB7C5C1B01FC3D');
        $this->addSql('DROP INDEX IDX_10DB7C5C1B01FC3D ON aipd_analyse_mesure_protection');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection DROP scenario_menace_id');
        $this->addSql('DROP INDEX IDX_D48271917D1480F7 ON aipd_modele_scenario_menace_mesure_protection');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection CHANGE modele_mesure_protection_id mesure_protection_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D4827191404E9273 FOREIGN KEY (mesure_protection_id) REFERENCES aipd_mesure_protection (id)');
        $this->addSql('CREATE INDEX IDX_D4827191404E9273 ON aipd_modele_scenario_menace_mesure_protection (mesure_protection_id)');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD PRIMARY KEY (modele_scenario_menace_id, mesure_protection_id)');
    }
}
