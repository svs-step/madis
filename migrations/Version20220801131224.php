<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220801131224 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB0241DE0ECE4');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB02441FBE4EC');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB0246A0D339A');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB024756841F0');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB0241DE0ECE4 FOREIGN KEY (avis_responsable_id) REFERENCES aipd_analyse_avis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB02441FBE4EC FOREIGN KEY (avis_dpd_id) REFERENCES aipd_analyse_avis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB0246A0D339A FOREIGN KEY (avis_referent_id) REFERENCES aipd_analyse_avis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB024756841F0 FOREIGN KEY (avis_representant_id) REFERENCES aipd_analyse_avis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conformite_traitement_reponse DROP FOREIGN KEY FK_6B4E420CEF983B6');
        $this->addSql('ALTER TABLE conformite_traitement_reponse ADD CONSTRAINT FK_6B4E420CEF983B6 FOREIGN KEY (conformite_traitement_id) REFERENCES conformite_traitement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection DROP FOREIGN KEY FK_A4EBC0E42EA38911');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection DROP FOREIGN KEY FK_A4EBC0E4CF18BB82');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection ADD CONSTRAINT FK_A4EBC0E42EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection ADD CONSTRAINT FK_A4EBC0E4CF18BB82 FOREIGN KEY (reponse_id) REFERENCES conformite_traitement_reponse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen DROP FOREIGN KEY FK_C2CDE0802EA38911');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen DROP FOREIGN KEY FK_C2CDE080CF18BB82');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen ADD CONSTRAINT FK_C2CDE0802EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen ADD CONSTRAINT FK_C2CDE080CF18BB82 FOREIGN KEY (reponse_id) REFERENCES conformite_traitement_reponse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFAB2CF0654');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFAB2CF0654 FOREIGN KEY (cloned_from_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesurement_treatment DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_treatment ADD PRIMARY KEY (mesurement_id, treatment_id)');
        $this->addSql('ALTER TABLE mesurement_contractor DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_contractor ADD PRIMARY KEY (mesurement_id, contractor_id)');
        $this->addSql('ALTER TABLE mesurement_request DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_request ADD PRIMARY KEY (mesurement_id, request_id)');
        $this->addSql('ALTER TABLE mesurement_violation DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_violation ADD PRIMARY KEY (mesurement_id, violation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB0246A0D339A');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB02441FBE4EC');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB024756841F0');
        $this->addSql('ALTER TABLE aipd_analyse_impact DROP FOREIGN KEY FK_DDAAB0241DE0ECE4');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB0246A0D339A FOREIGN KEY (avis_referent_id) REFERENCES aipd_analyse_avis (id)');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB02441FBE4EC FOREIGN KEY (avis_dpd_id) REFERENCES aipd_analyse_avis (id)');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB024756841F0 FOREIGN KEY (avis_representant_id) REFERENCES aipd_analyse_avis (id)');
        $this->addSql('ALTER TABLE aipd_analyse_impact ADD CONSTRAINT FK_DDAAB0241DE0ECE4 FOREIGN KEY (avis_responsable_id) REFERENCES aipd_analyse_avis (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse DROP FOREIGN KEY FK_6B4E420CEF983B6');
        $this->addSql('ALTER TABLE conformite_traitement_reponse ADD CONSTRAINT FK_6B4E420CEF983B6 FOREIGN KEY (conformite_traitement_id) REFERENCES conformite_traitement (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection DROP FOREIGN KEY FK_A4EBC0E4CF18BB82');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection DROP FOREIGN KEY FK_A4EBC0E42EA38911');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection ADD CONSTRAINT FK_A4EBC0E4CF18BB82 FOREIGN KEY (reponse_id) REFERENCES conformite_traitement_reponse (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection ADD CONSTRAINT FK_A4EBC0E42EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen DROP FOREIGN KEY FK_C2CDE080CF18BB82');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen DROP FOREIGN KEY FK_C2CDE0802EA38911');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen ADD CONSTRAINT FK_C2CDE080CF18BB82 FOREIGN KEY (reponse_id) REFERENCES conformite_traitement_reponse (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen ADD CONSTRAINT FK_C2CDE0802EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id)');
        $this->addSql('ALTER TABLE mesurement_contractor DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_contractor ADD PRIMARY KEY (contractor_id, mesurement_id)');
        $this->addSql('ALTER TABLE mesurement_request DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_request ADD PRIMARY KEY (request_id, mesurement_id)');
        $this->addSql('ALTER TABLE mesurement_treatment DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_treatment ADD PRIMARY KEY (treatment_id, mesurement_id)');
        $this->addSql('ALTER TABLE mesurement_violation DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_violation ADD PRIMARY KEY (violation_id, mesurement_id)');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFAB2CF0654');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFAB2CF0654 FOREIGN KEY (cloned_from_id) REFERENCES registry_mesurement (id)');
    }
}
