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
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D482719194A6586A FOREIGN KEY (modele_scenario_menace_id) REFERENCES aipd_modele_scenario_menace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection ADD CONSTRAINT FK_D48271917D1480F8 FOREIGN KEY (modele_mesure_protection_id) REFERENCES aipd_modele_mesure_protection (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection DROP FOREIGN KEY FK_D482719194A6586A');
        $this->addSql('ALTER TABLE aipd_modele_scenario_menace_mesure_protection DROP FOREIGN KEY FK_D48271917D1480F8');
    }
}
