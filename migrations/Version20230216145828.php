<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216145828 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54A3350D094');
        $this->addSql('ALTER TABLE survey_referentiel_section DROP FOREIGN KEY FK_6173653F3350D094');
        $this->addSql('ALTER TABLE survey_referentiel_answer DROP FOREIGN KEY FK_B3D2C4045063A6D0');
        $this->addSql('ALTER TABLE survey_referentiel_question DROP FOREIGN KEY FK_30689B8598F0D898');
        $this->addSql('CREATE TABLE referentiel_answer_survey (referentiel_answer_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', survey_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_7B526791B4808779 (referentiel_answer_id), INDEX IDX_7B526791B3FE509D (survey_id), PRIMARY KEY(referentiel_answer_id, survey_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE referentiel_answer_survey ADD CONSTRAINT FK_7B526791B4808779 FOREIGN KEY (referentiel_answer_id) REFERENCES referentiel_answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel_answer_survey ADD CONSTRAINT FK_7B526791B3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE survey_referentiel');
        $this->addSql('DROP TABLE survey_referentiel_answer');
        $this->addSql('DROP TABLE survey_referentiel_question');
        $this->addSql('DROP TABLE survey_referentiel_section');
        $this->addSql('DROP INDEX UNIQ_E279C54A3350D094 ON maturity_survey');
        $this->addSql('ALTER TABLE maturity_survey DROP survey_referentiel_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE survey_referentiel (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE survey_referentiel_answer (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', survey_referentiel_question_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, recommendation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, order_number INT NOT NULL, INDEX IDX_B3D2C4045063A6D0 (survey_referentiel_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE survey_referentiel_question (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', survey_referentiel_section_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, weight INT NOT NULL, order_number INT NOT NULL, `option` TINYINT(1) DEFAULT \'NULL\', option_reason VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, INDEX IDX_30689B8598F0D898 (survey_referentiel_section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE survey_referentiel_section (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', survey_referentiel_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, order_number INT NOT NULL, INDEX IDX_6173653F3350D094 (survey_referentiel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE test_assoc (treatment_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', treatment_data_category_code VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(treatment_id, treatment_data_category_code)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE test_registry (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', creator_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, goal LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, software VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, legal_basis JSON CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', legal_basis_justification LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, concerned_people JSON CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', recipient_category LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delay_number INT DEFAULT NULL, delay_period VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, manager VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, security_access_control_check TINYINT(1) NOT NULL, security_access_control_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, security_tracability_check TINYINT(1) NOT NULL, security_tracability_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, security_saving_check TINYINT(1) NOT NULL, security_saving_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, paper_processing TINYINT(1) NOT NULL, security_update_check TINYINT(1) NOT NULL, security_update_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, security_other_check TINYINT(1) NOT NULL, security_other_comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, delay_other_delay TINYINT(1) NOT NULL, delay_comment LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, data_category_other LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, systematic_monitoring TINYINT(1) NOT NULL, large_scale_collection TINYINT(1) NOT NULL, vulnerable_people TINYINT(1) NOT NULL, data_crossing TINYINT(1) NOT NULL, completion INT NOT NULL, template TINYINT(1) NOT NULL, template_identifier INT DEFAULT NULL, data_origin VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, observation LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE survey_referentiel_answer ADD CONSTRAINT FK_B3D2C4045063A6D0 FOREIGN KEY (survey_referentiel_question_id) REFERENCES survey_referentiel_question (id)');
        $this->addSql('ALTER TABLE survey_referentiel_question ADD CONSTRAINT FK_30689B8598F0D898 FOREIGN KEY (survey_referentiel_section_id) REFERENCES survey_referentiel_section (id)');
        $this->addSql('ALTER TABLE survey_referentiel_section ADD CONSTRAINT FK_6173653F3350D094 FOREIGN KEY (survey_referentiel_id) REFERENCES survey_referentiel (id)');
        $this->addSql('DROP TABLE referentiel_answer_survey');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54A3350D094 FOREIGN KEY (survey_referentiel_id) REFERENCES survey_referentiel (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E279C54A3350D094 ON maturity_survey (survey_referentiel_id)');
    }
}
