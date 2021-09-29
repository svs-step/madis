<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210929144854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_analyse_scenario_menace (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, is_visible TINYINT(1) NOT NULL, is_disponibilite TINYINT(1) NOT NULL, is_integreite TINYINT(1) NOT NULL, is_confidentialite TINYINT(1) NOT NULL, vraisemblance VARCHAR(255) NOT NULL, gravite VARCHAR(255) NOT NULL, precisions VARCHAR(255) NOT NULL, INDEX IDX_EC6B120B498AFC31 (analyse_impact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_analyse_scenario_menace_mesure_protection (analyse_scenario_menace_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesure_protection_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_9FF8F289621760CB (analyse_scenario_menace_id), INDEX IDX_9FF8F289404E9273 (mesure_protection_id), PRIMARY KEY(analyse_scenario_menace_id, mesure_protection_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_modele_scenario_menace_mesure_protection (modele_scenario_menace_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesure_protection_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_D482719194A6586B (modele_scenario_menace_id), INDEX IDX_D4827191404E9273 (mesure_protection_id), PRIMARY KEY(modele_scenario_menace_id, mesure_protection_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace ADD CONSTRAINT FK_EC6B120B498AFC31 FOREIGN KEY (analyse_impact_id) REFERENCES aipd_analyse_impact (id)');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace_mesure_protection ADD CONSTRAINT FK_9FF8F289621760CB FOREIGN KEY (analyse_scenario_menace_id) REFERENCES aipd_analyse_scenario_menace (id)');
        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace_mesure_protection ADD CONSTRAINT FK_9FF8F289404E9273 FOREIGN KEY (mesure_protection_id) REFERENCES aipd_mesure_protection (id)');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D482719194A6586B FOREIGN KEY (modele_scenario_menace_id) REFERENCES aipd_modele_scenario_menace (id)');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D4827191404E9273 FOREIGN KEY (mesure_protection_id) REFERENCES aipd_mesure_protection (id)');
        $this->addSql('DROP TABLE aipd_scenario_menace_mesure_protection');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_scenario_menace_mesure_protection DROP FOREIGN KEY FK_9FF8F289621760CB');
        $this->addSql('CREATE TABLE aipd_scenario_menace_mesure_protection (abstract_scenario_menace_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', mesure_protection_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', INDEX IDX_51BC485E404E9273 (mesure_protection_id), INDEX IDX_51BC485EDA8234FB (abstract_scenario_menace_id), PRIMARY KEY(abstract_scenario_menace_id, mesure_protection_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE aipd_scenario_menace_mesure_protection ADD CONSTRAINT FK_51BC485E404E9273 FOREIGN KEY (mesure_protection_id) REFERENCES aipd_mesure_protection (id)');
        $this->addSql('ALTER TABLE aipd_scenario_menace_mesure_protection ADD CONSTRAINT FK_51BC485EDA8234FB FOREIGN KEY (abstract_scenario_menace_id) REFERENCES aipd_modele_scenario_menace (id)');
        $this->addSql('DROP TABLE aipd_analyse_scenario_menace');
        $this->addSql('DROP TABLE aipd_analyse_scenario_menace_mesure_protection');
        $this->addSql('DROP TABLE aipd_modele_scenario_menace_mesure_protection');
    }
}
