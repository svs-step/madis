<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221141253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answer_survey (answer_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', survey_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_5FA6A15BAA334807 (answer_id), INDEX IDX_5FA6A15BB3FE509D (survey_id), PRIMARY KEY(answer_id, survey_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maturity_referentiel (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer_survey ADD CONSTRAINT FK_5FA6A15BAA334807 FOREIGN KEY (answer_id) REFERENCES maturity_answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answer_survey ADD CONSTRAINT FK_5FA6A15BB3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maturity DROP FOREIGN KEY FK_4636E0E8115F0EE5');
        $this->addSql('DROP INDEX IDX_4636E0E8115F0EE5 ON maturity');
        $this->addSql('ALTER TABLE maturity ADD referentiel_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE survey_id survey_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity ADD CONSTRAINT FK_4636E0E8805DB139 FOREIGN KEY (referentiel_id) REFERENCES maturity_referentiel (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4636E0E8805DB139 ON maturity (referentiel_id)');
        // $this->addSql('ALTER TABLE maturity_answer DROP FOREIGN KEY FK_95FB1493B3FE509D');
        // $this->addSql('DROP INDEX IDX_95FB1493B3FE509D ON maturity_answer');
        $this->addSql('ALTER TABLE maturity_answer ADD recommendation VARCHAR(255) NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD position INT NOT NULL, CHANGE question_id question_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_domain ADD referentiel_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_domain ADD CONSTRAINT FK_E88F40BD805DB139 FOREIGN KEY (referentiel_id) REFERENCES maturity_referentiel (id)');
        $this->addSql('CREATE INDEX IDX_E88F40BD805DB139 ON maturity_domain (referentiel_id)');
        $this->addSql('ALTER TABLE maturity_question ADD position INT NOT NULL, ADD `optional` TINYINT(1) NOT NULL, ADD option_reason VARCHAR(255) DEFAULT NULL, CHANGE domain_id domain_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_survey ADD referentiel_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE collectivity_id collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54A805DB139 FOREIGN KEY (referentiel_id) REFERENCES maturity_referentiel (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E279C54A805DB139 ON maturity_survey (referentiel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity DROP FOREIGN KEY FK_4636E0E8805DB139');
        $this->addSql('ALTER TABLE maturity_domain DROP FOREIGN KEY FK_E88F40BD805DB139');
        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54A805DB139');
        $this->addSql('DROP TABLE answer_survey');
        $this->addSql('DROP TABLE maturity_referentiel');
        $this->addSql('DROP INDEX IDX_4636E0E8805DB139 ON maturity');
        $this->addSql('ALTER TABLE maturity ADD domain_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', DROP referentiel_id, CHANGE survey_id survey_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        // $this->addSql('ALTER TABLE maturity ADD CONSTRAINT FK_4636E0E8115F0EE5 FOREIGN KEY (domain_id) REFERENCES maturity_domain (id) ON DELETE CASCADE');
        // $this->addSql('CREATE INDEX IDX_4636E0E8115F0EE5 ON maturity (domain_id)');
        $this->addSql('ALTER TABLE maturity_answer ADD survey_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', DROP recommendation, DROP name, DROP position, CHANGE question_id question_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        // $this->addSql('ALTER TABLE maturity_answer ADD CONSTRAINT FK_95FB1493B3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id) ON DELETE CASCADE');
        // $this->addSql('CREATE INDEX IDX_95FB1493B3FE509D ON maturity_answer (survey_id)');
        $this->addSql('DROP INDEX IDX_E88F40BD805DB139 ON maturity_domain');
        $this->addSql('ALTER TABLE maturity_domain DROP referentiel_id');
        $this->addSql('ALTER TABLE maturity_question DROP position, DROP `option`, DROP option_reason, CHANGE domain_id domain_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('DROP INDEX UNIQ_E279C54A805DB139 ON maturity_survey');
        $this->addSql('ALTER TABLE maturity_survey DROP referentiel_id, CHANGE collectivity_id collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
    }
}
