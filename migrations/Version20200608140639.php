<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200608140639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_duplication DROP FOREIGN KEY FK_AE3FF3CE1B49D97D');
        $this->addSql('ALTER TABLE admin_duplication ADD CONSTRAINT FK_AE3FF3CE1B49D97D FOREIGN KEY (source_collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_duplication_collectivity DROP FOREIGN KEY FK_59EF3717A78FD7B3');
        $this->addSql('ALTER TABLE admin_duplication_collectivity DROP FOREIGN KEY FK_59EF3717BD56F776');
        $this->addSql('ALTER TABLE admin_duplication_collectivity ADD CONSTRAINT FK_59EF3717A78FD7B3 FOREIGN KEY (duplication_id) REFERENCES admin_duplication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_duplication_collectivity ADD CONSTRAINT FK_59EF3717BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maturity DROP FOREIGN KEY FK_4636E0E8115F0EE5');
        $this->addSql('ALTER TABLE maturity DROP FOREIGN KEY FK_4636E0E8B3FE509D');
        $this->addSql('ALTER TABLE maturity ADD CONSTRAINT FK_4636E0E8115F0EE5 FOREIGN KEY (domain_id) REFERENCES maturity_domain (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maturity ADD CONSTRAINT FK_4636E0E8B3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54A61220EA6');
        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54ABD56F776');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54A61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54ABD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maturity_answer DROP FOREIGN KEY FK_95FB14931E27F6BF');
        $this->addSql('ALTER TABLE maturity_answer DROP FOREIGN KEY FK_95FB1493B3FE509D');
        $this->addSql('ALTER TABLE maturity_answer ADD CONSTRAINT FK_95FB14931E27F6BF FOREIGN KEY (question_id) REFERENCES maturity_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maturity_answer ADD CONSTRAINT FK_95FB1493B3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_request DROP FOREIGN KEY FK_3CDC30CD61220EA6');
        $this->addSql('ALTER TABLE registry_request DROP FOREIGN KEY FK_3CDC30CDBD56F776');
        $this->addSql('ALTER TABLE registry_request ADD CONSTRAINT FK_3CDC30CD61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE registry_request ADD CONSTRAINT FK_3CDC30CDBD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_violation DROP FOREIGN KEY FK_34E9D26261220EA6');
        $this->addSql('ALTER TABLE registry_violation DROP FOREIGN KEY FK_34E9D262BD56F776');
        $this->addSql('ALTER TABLE registry_violation ADD CONSTRAINT FK_34E9D26261220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE registry_violation ADD CONSTRAINT FK_34E9D262BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof DROP FOREIGN KEY FK_5982E8ED61220EA6');
        $this->addSql('ALTER TABLE registry_proof DROP FOREIGN KEY FK_5982E8EDBD56F776');
        $this->addSql('ALTER TABLE registry_proof ADD CONSTRAINT FK_5982E8ED61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE registry_proof ADD CONSTRAINT FK_5982E8EDBD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_treatment DROP FOREIGN KEY FK_313C16DD471C0366');
        $this->addSql('ALTER TABLE registry_proof_treatment DROP FOREIGN KEY FK_313C16DDD7086615');
        $this->addSql('ALTER TABLE registry_proof_treatment ADD CONSTRAINT FK_313C16DD471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_treatment ADD CONSTRAINT FK_313C16DDD7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_contractor DROP FOREIGN KEY FK_EA6E4196B0265DC7');
        $this->addSql('ALTER TABLE registry_proof_contractor DROP FOREIGN KEY FK_EA6E4196D7086615');
        $this->addSql('ALTER TABLE registry_proof_contractor ADD CONSTRAINT FK_EA6E4196B0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_contractor ADD CONSTRAINT FK_EA6E4196D7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_mesurement DROP FOREIGN KEY FK_D88358352EA38911');
        $this->addSql('ALTER TABLE registry_proof_mesurement DROP FOREIGN KEY FK_D8835835D7086615');
        $this->addSql('ALTER TABLE registry_proof_mesurement ADD CONSTRAINT FK_D88358352EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_mesurement ADD CONSTRAINT FK_D8835835D7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_request DROP FOREIGN KEY FK_13EAE4B427EB8A5');
        $this->addSql('ALTER TABLE registry_proof_request DROP FOREIGN KEY FK_13EAE4BD7086615');
        $this->addSql('ALTER TABLE registry_proof_request ADD CONSTRAINT FK_13EAE4B427EB8A5 FOREIGN KEY (request_id) REFERENCES registry_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_request ADD CONSTRAINT FK_13EAE4BD7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_violation DROP FOREIGN KEY FK_4E876E0E7386118A');
        $this->addSql('ALTER TABLE registry_proof_violation DROP FOREIGN KEY FK_4E876E0ED7086615');
        $this->addSql('ALTER TABLE registry_proof_violation ADD CONSTRAINT FK_4E876E0E7386118A FOREIGN KEY (violation_id) REFERENCES registry_violation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof_violation ADD CONSTRAINT FK_4E876E0ED7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFA61220EA6');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFABD56F776');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFA61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFABD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_contractor DROP FOREIGN KEY FK_AE10025961220EA6');
        $this->addSql('ALTER TABLE registry_contractor DROP FOREIGN KEY FK_AE100259BD56F776');
        $this->addSql('ALTER TABLE registry_contractor ADD CONSTRAINT FK_AE10025961220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE registry_contractor ADD CONSTRAINT FK_AE100259BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB161220EA6');
        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB1BD56F776');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB161220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB1BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE treatment_contractor DROP FOREIGN KEY FK_50056FFA471C0366');
        $this->addSql('ALTER TABLE treatment_contractor DROP FOREIGN KEY FK_50056FFAB0265DC7');
        $this->addSql('ALTER TABLE treatment_contractor ADD CONSTRAINT FK_50056FFA471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE treatment_contractor ADD CONSTRAINT FK_50056FFAB0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_assoc_treatment_data_category DROP FOREIGN KEY FK_DB9E371A471C0366');
        $this->addSql('ALTER TABLE registry_assoc_treatment_data_category ADD CONSTRAINT FK_DB9E371A471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_duplication DROP FOREIGN KEY FK_AE3FF3CE1B49D97D');
        $this->addSql('ALTER TABLE admin_duplication ADD CONSTRAINT FK_AE3FF3CE1B49D97D FOREIGN KEY (source_collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE admin_duplication_collectivity DROP FOREIGN KEY FK_59EF3717A78FD7B3');
        $this->addSql('ALTER TABLE admin_duplication_collectivity DROP FOREIGN KEY FK_59EF3717BD56F776');
        $this->addSql('ALTER TABLE admin_duplication_collectivity ADD CONSTRAINT FK_59EF3717A78FD7B3 FOREIGN KEY (duplication_id) REFERENCES admin_duplication (id)');
        $this->addSql('ALTER TABLE admin_duplication_collectivity ADD CONSTRAINT FK_59EF3717BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE maturity DROP FOREIGN KEY FK_4636E0E8115F0EE5');
        $this->addSql('ALTER TABLE maturity DROP FOREIGN KEY FK_4636E0E8B3FE509D');
        $this->addSql('ALTER TABLE maturity ADD CONSTRAINT FK_4636E0E8115F0EE5 FOREIGN KEY (domain_id) REFERENCES maturity_domain (id)');
        $this->addSql('ALTER TABLE maturity ADD CONSTRAINT FK_4636E0E8B3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id)');
        $this->addSql('ALTER TABLE maturity_answer DROP FOREIGN KEY FK_95FB14931E27F6BF');
        $this->addSql('ALTER TABLE maturity_answer DROP FOREIGN KEY FK_95FB1493B3FE509D');
        $this->addSql('ALTER TABLE maturity_answer ADD CONSTRAINT FK_95FB14931E27F6BF FOREIGN KEY (question_id) REFERENCES maturity_question (id)');
        $this->addSql('ALTER TABLE maturity_answer ADD CONSTRAINT FK_95FB1493B3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id)');
        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54ABD56F776');
        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54A61220EA6');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54ABD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54A61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE registry_assoc_treatment_data_category DROP FOREIGN KEY FK_DB9E371A471C0366');
        $this->addSql('ALTER TABLE registry_assoc_treatment_data_category ADD CONSTRAINT FK_DB9E371A471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id)');
        $this->addSql('ALTER TABLE registry_contractor DROP FOREIGN KEY FK_AE100259BD56F776');
        $this->addSql('ALTER TABLE registry_contractor DROP FOREIGN KEY FK_AE10025961220EA6');
        $this->addSql('ALTER TABLE registry_contractor ADD CONSTRAINT FK_AE100259BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_contractor ADD CONSTRAINT FK_AE10025961220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFABD56F776');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFA61220EA6');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFABD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFA61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE registry_proof DROP FOREIGN KEY FK_5982E8EDBD56F776');
        $this->addSql('ALTER TABLE registry_proof DROP FOREIGN KEY FK_5982E8ED61220EA6');
        $this->addSql('ALTER TABLE registry_proof ADD CONSTRAINT FK_5982E8EDBD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_proof ADD CONSTRAINT FK_5982E8ED61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE registry_proof_contractor DROP FOREIGN KEY FK_EA6E4196D7086615');
        $this->addSql('ALTER TABLE registry_proof_contractor DROP FOREIGN KEY FK_EA6E4196B0265DC7');
        $this->addSql('ALTER TABLE registry_proof_contractor ADD CONSTRAINT FK_EA6E4196D7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id)');
        $this->addSql('ALTER TABLE registry_proof_contractor ADD CONSTRAINT FK_EA6E4196B0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id)');
        $this->addSql('ALTER TABLE registry_proof_mesurement DROP FOREIGN KEY FK_D8835835D7086615');
        $this->addSql('ALTER TABLE registry_proof_mesurement DROP FOREIGN KEY FK_D88358352EA38911');
        $this->addSql('ALTER TABLE registry_proof_mesurement ADD CONSTRAINT FK_D8835835D7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id)');
        $this->addSql('ALTER TABLE registry_proof_mesurement ADD CONSTRAINT FK_D88358352EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id)');
        $this->addSql('ALTER TABLE registry_proof_request DROP FOREIGN KEY FK_13EAE4BD7086615');
        $this->addSql('ALTER TABLE registry_proof_request DROP FOREIGN KEY FK_13EAE4B427EB8A5');
        $this->addSql('ALTER TABLE registry_proof_request ADD CONSTRAINT FK_13EAE4BD7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id)');
        $this->addSql('ALTER TABLE registry_proof_request ADD CONSTRAINT FK_13EAE4B427EB8A5 FOREIGN KEY (request_id) REFERENCES registry_request (id)');
        $this->addSql('ALTER TABLE registry_proof_treatment DROP FOREIGN KEY FK_313C16DDD7086615');
        $this->addSql('ALTER TABLE registry_proof_treatment DROP FOREIGN KEY FK_313C16DD471C0366');
        $this->addSql('ALTER TABLE registry_proof_treatment ADD CONSTRAINT FK_313C16DDD7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id)');
        $this->addSql('ALTER TABLE registry_proof_treatment ADD CONSTRAINT FK_313C16DD471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id)');
        $this->addSql('ALTER TABLE registry_proof_violation DROP FOREIGN KEY FK_4E876E0ED7086615');
        $this->addSql('ALTER TABLE registry_proof_violation DROP FOREIGN KEY FK_4E876E0E7386118A');
        $this->addSql('ALTER TABLE registry_proof_violation ADD CONSTRAINT FK_4E876E0ED7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id)');
        $this->addSql('ALTER TABLE registry_proof_violation ADD CONSTRAINT FK_4E876E0E7386118A FOREIGN KEY (violation_id) REFERENCES registry_violation (id)');
        $this->addSql('ALTER TABLE registry_request DROP FOREIGN KEY FK_3CDC30CDBD56F776');
        $this->addSql('ALTER TABLE registry_request DROP FOREIGN KEY FK_3CDC30CD61220EA6');
        $this->addSql('ALTER TABLE registry_request ADD CONSTRAINT FK_3CDC30CDBD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_request ADD CONSTRAINT FK_3CDC30CD61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB1BD56F776');
        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB161220EA6');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB1BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB161220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE registry_violation DROP FOREIGN KEY FK_34E9D262BD56F776');
        $this->addSql('ALTER TABLE registry_violation DROP FOREIGN KEY FK_34E9D26261220EA6');
        $this->addSql('ALTER TABLE registry_violation ADD CONSTRAINT FK_34E9D262BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_violation ADD CONSTRAINT FK_34E9D26261220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE treatment_contractor DROP FOREIGN KEY FK_50056FFA471C0366');
        $this->addSql('ALTER TABLE treatment_contractor DROP FOREIGN KEY FK_50056FFAB0265DC7');
        $this->addSql('ALTER TABLE treatment_contractor ADD CONSTRAINT FK_50056FFA471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id)');
        $this->addSql('ALTER TABLE treatment_contractor ADD CONSTRAINT FK_50056FFAB0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id)');
    }
}
