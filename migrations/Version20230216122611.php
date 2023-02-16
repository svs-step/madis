<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216122611 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE modele_scenario_menace_modele_mesure_protection (modele_scenario_menace_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', modele_mesure_protection_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_CFBAB02F94A6586B (modele_scenario_menace_id), INDEX IDX_CFBAB02F7D1480F7 (modele_mesure_protection_id), PRIMARY KEY(modele_scenario_menace_id, modele_mesure_protection_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE modele_scenario_menace_modele_mesure_protection ADD CONSTRAINT FK_CFBAB02F94A6586B FOREIGN KEY (modele_scenario_menace_id) REFERENCES aipd_modele_scenario_menace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE modele_scenario_menace_modele_mesure_protection ADD CONSTRAINT FK_CFBAB02F7D1480F7 FOREIGN KEY (modele_mesure_protection_id) REFERENCES aipd_modele_mesure_protection (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE aipd_modele_scenario_menace_mesure_protection');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_modele_scenario_menace_mesure_protection (modele_scenario_menace_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', modele_mesure_protection_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', INDEX IDX_D48271917D1480F7 (modele_mesure_protection_id), INDEX IDX_D482719194A6586B (modele_scenario_menace_id), PRIMARY KEY(modele_scenario_menace_id, modele_mesure_protection_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D48271917D1480F7 FOREIGN KEY (modele_mesure_protection_id) REFERENCES aipd_modele_mesure_protection (id)');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D482719194A6586B FOREIGN KEY (modele_scenario_menace_id) REFERENCES aipd_modele_scenario_menace (id)');
        $this->addSql('DROP TABLE modele_scenario_menace_modele_mesure_protection');
        ;
    }
}
