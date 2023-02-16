<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214092028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE survey_referentiel (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_referentiel_answer (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', survey_referentiel_question_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, recommendation VARCHAR(255) NOT NULL, order_number INT NOT NULL, INDEX IDX_B3D2C4045063A6D0 (survey_referentiel_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_referentiel_question (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', survey_referentiel_section_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, weight INT NOT NULL, order_number INT NOT NULL, `option` TINYINT(1), option_reason VARCHAR(255), INDEX IDX_30689B8598F0D898 (survey_referentiel_section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_referentiel_section (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', survey_referentiel_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255), order_number INT NOT NULL, INDEX IDX_6173653F3350D094 (survey_referentiel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE survey_referentiel_answer ADD CONSTRAINT FK_B3D2C4045063A6D0 FOREIGN KEY (survey_referentiel_question_id) REFERENCES survey_referentiel_question (id)');
        $this->addSql('ALTER TABLE survey_referentiel_question ADD CONSTRAINT FK_30689B8598F0D898 FOREIGN KEY (survey_referentiel_section_id) REFERENCES survey_referentiel_section (id)');
        $this->addSql('ALTER TABLE survey_referentiel_section ADD CONSTRAINT FK_6173653F3350D094 FOREIGN KEY (survey_referentiel_id) REFERENCES survey_referentiel (id)');
        $this->addSql('ALTER TABLE maturity CHANGE domain_id domain_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE survey_id survey_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_answer CHANGE question_id question_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE survey_id survey_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_question CHANGE domain_id domain_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_survey DROP INDEX IDX_E279C54A805DB139, ADD UNIQUE INDEX UNIQ_E279C54A805DB139 (referentiel_id)');
        $this->addSql('ALTER TABLE maturity_survey ADD survey_referentiel_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE collectivity_id collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE referentiel_id referentiel_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54A3350D094 FOREIGN KEY (survey_referentiel_id) REFERENCES survey_referentiel (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E279C54A3350D094 ON maturity_survey (survey_referentiel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54A3350D094');
        $this->addSql('ALTER TABLE survey_referentiel_section DROP FOREIGN KEY FK_6173653F3350D094');
        $this->addSql('ALTER TABLE survey_referentiel_answer DROP FOREIGN KEY FK_B3D2C4045063A6D0');
        $this->addSql('ALTER TABLE survey_referentiel_question DROP FOREIGN KEY FK_30689B8598F0D898');
        $this->addSql('DROP TABLE survey_referentiel');
        $this->addSql('DROP TABLE survey_referentiel_answer');
        $this->addSql('DROP TABLE survey_referentiel_question');
        $this->addSql('DROP TABLE survey_referentiel_section');
        $this->addSql('ALTER TABLE maturity CHANGE domain_id domain_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE survey_id survey_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_answer CHANGE question_id question_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE survey_id survey_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_question CHANGE domain_id domain_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_survey DROP INDEX UNIQ_E279C54A805DB139, ADD INDEX IDX_E279C54A805DB139 (referentiel_id)');
        $this->addSql('DROP INDEX UNIQ_E279C54A3350D094 ON maturity_survey');
        $this->addSql('ALTER TABLE maturity_survey DROP survey_referentiel_id, CHANGE referentiel_id referentiel_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE collectivity_id collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
    }
}
