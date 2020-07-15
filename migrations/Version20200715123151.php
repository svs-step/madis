<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200715123151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $keyArray = [
            'maturity_survey'                              => 'survey',
            'registry_request'                             => 'request',
            'registry_violation'                           => 'violation',
            'registry_proof'                               => 'proof',
            'registry_mesurement'                          => 'mesurement',
            'registry_conformite_organisation_evaluation'  => 'evaluation',
            'registry_contractor'                          => 'contractor',
            'conformite_traitement'                        => 'conformite_traitement',
            'registry_treatment'                           => 'treatement',
            'user_collectivity'                            => 'collectivity',
            'user_user'                                    => 'user',
            'registry_conformite_organisation_conformite'  => 'conformite',
            'registry_conformite_organisation_participant' => 'participant',
            'user_comite_il_contact'                       => 'comite_il_contact',
            'conformite_traitement_reponse'                => 'conformite_traitement_reponse',
        ];

        $this->addSql('CREATE TABLE loggable_subject (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        foreach ($keyArray as $key => $discr) {
            $this->addSql("INSERT IGNORE INTO loggable_subject (id, discr) SELECT id, '" . $discr . "' FROM " . $key);
        }

        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54ABF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_request ADD CONSTRAINT FK_3CDC30CDBF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_violation ADD CONSTRAINT FK_34E9D262BF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_proof ADD CONSTRAINT FK_5982E8EDBF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFABF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_conformite_organisation_participant ADD CONSTRAINT FK_24A83582BF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation ADD draft TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation ADD CONSTRAINT FK_9D0C7E67BF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_conformite_organisation_conformite ADD CONSTRAINT FK_7BD97140BF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_contractor ADD CONSTRAINT FK_AE100259BF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conformite_traitement ADD CONSTRAINT FK_85B1C39CBF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conformite_traitement_reponse ADD CONSTRAINT FK_6B4E420BF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB1BF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_comite_il_contact ADD CONSTRAINT FK_D1C84AE9BF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_collectivity ADD CONSTRAINT FK_5B6928ECBF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80BF396750 FOREIGN KEY (id) REFERENCES loggable_subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reporting_log_journal ADD CONSTRAINT FK_87AE0D7D23EDC87 FOREIGN KEY (subject_id) REFERENCES loggable_subject (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54ABF396750');
        $this->addSql('ALTER TABLE registry_request DROP FOREIGN KEY FK_3CDC30CDBF396750');
        $this->addSql('ALTER TABLE registry_violation DROP FOREIGN KEY FK_34E9D262BF396750');
        $this->addSql('ALTER TABLE registry_proof DROP FOREIGN KEY FK_5982E8EDBF396750');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFABF396750');
        $this->addSql('ALTER TABLE registry_conformite_organisation_participant DROP FOREIGN KEY FK_24A83582BF396750');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation DROP FOREIGN KEY FK_9D0C7E67BF396750');
        $this->addSql('ALTER TABLE registry_conformite_organisation_conformite DROP FOREIGN KEY FK_7BD97140BF396750');
        $this->addSql('ALTER TABLE registry_contractor DROP FOREIGN KEY FK_AE100259BF396750');
        $this->addSql('ALTER TABLE conformite_traitement DROP FOREIGN KEY FK_85B1C39CBF396750');
        $this->addSql('ALTER TABLE conformite_traitement_reponse DROP FOREIGN KEY FK_6B4E420BF396750');
        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB1BF396750');
        $this->addSql('ALTER TABLE user_comite_il_contact DROP FOREIGN KEY FK_D1C84AE9BF396750');
        $this->addSql('ALTER TABLE user_collectivity DROP FOREIGN KEY FK_5B6928ECBF396750');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80BF396750');
        $this->addSql('ALTER TABLE reporting_log_journal DROP FOREIGN KEY FK_87AE0D7D23EDC87');
        $this->addSql('DROP TABLE loggable_subject');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation DROP draft');
    }
}
