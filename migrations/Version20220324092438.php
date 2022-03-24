<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324092438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_analyse_mesures_protection_scenarios_menace (analyse_scenario_menace_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', analyse_mesure_protection_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_2609864E621760CB (analyse_scenario_menace_id), INDEX IDX_2609864EFB117231 (analyse_mesure_protection_id), PRIMARY KEY(analyse_scenario_menace_id, analyse_mesure_protection_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aipd_analyse_mesures_protection_scenarios_menace ADD CONSTRAINT FK_2609864E621760CB FOREIGN KEY (analyse_scenario_menace_id) REFERENCES aipd_analyse_scenario_menace (id)');
        $this->addSql('ALTER TABLE aipd_analyse_mesures_protection_scenarios_menace ADD CONSTRAINT FK_2609864EFB117231 FOREIGN KEY (analyse_mesure_protection_id) REFERENCES aipd_analyse_mesure_protection (id)');
        $this->addSql('DROP TABLE analyse_mesure_protection_analyse_scenario_menace');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE analyse_mesure_protection_analyse_scenario_menace (analyse_mesure_protection_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', analyse_scenario_menace_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', INDEX IDX_F474A920FB117231 (analyse_mesure_protection_id), INDEX IDX_F474A920621760CB (analyse_scenario_menace_id), PRIMARY KEY(analyse_mesure_protection_id, analyse_scenario_menace_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE analyse_mesure_protection_analyse_scenario_menace ADD CONSTRAINT FK_F474A920621760CB FOREIGN KEY (analyse_scenario_menace_id) REFERENCES aipd_analyse_scenario_menace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE analyse_mesure_protection_analyse_scenario_menace ADD CONSTRAINT FK_F474A920FB117231 FOREIGN KEY (analyse_mesure_protection_id) REFERENCES aipd_analyse_mesure_protection (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE aipd_analyse_mesures_protection_scenarios_menace');
    }
}
