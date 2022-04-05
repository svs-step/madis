<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220321104700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE analyse_mesure_protection_analyse_scenario_menace (analyse_mesure_protection_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', analyse_scenario_menace_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_F474A920FB117231 (analyse_mesure_protection_id), INDEX IDX_F474A920621760CB (analyse_scenario_menace_id), PRIMARY KEY(analyse_mesure_protection_id, analyse_scenario_menace_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE analyse_mesure_protection_analyse_scenario_menace ADD CONSTRAINT FK_F474A920FB117231 FOREIGN KEY (analyse_mesure_protection_id) REFERENCES aipd_analyse_mesure_protection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE analyse_mesure_protection_analyse_scenario_menace ADD CONSTRAINT FK_F474A920621760CB FOREIGN KEY (analyse_scenario_menace_id) REFERENCES aipd_analyse_scenario_menace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection DROP FOREIGN KEY FK_10DB7C5C1B01FC3D');
        $this->addSql('DROP INDEX IDX_10DB7C5C1B01FC3D ON aipd_analyse_mesure_protection');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection DROP scenario_menace_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE analyse_mesure_protection_analyse_scenario_menace');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection ADD scenario_menace_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection ADD CONSTRAINT FK_10DB7C5C1B01FC3D FOREIGN KEY (scenario_menace_id) REFERENCES aipd_analyse_scenario_menace (id)');
        $this->addSql('CREATE INDEX IDX_10DB7C5C1B01FC3D ON aipd_analyse_mesure_protection (scenario_menace_id)');
    }
}
