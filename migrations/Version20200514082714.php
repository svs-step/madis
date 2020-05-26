<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200514082714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_conformite_organisation_participant (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', evaluation_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', prenom DATE DEFAULT NULL, nom_de_famille VARCHAR(255) DEFAULT NULL, civilite INT DEFAULT NULL, fonction VARCHAR(255) DEFAULT NULL, INDEX IDX_24A83582456C5646 (evaluation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_conformite_organisation_evaluation (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date DATE DEFAULT NULL, complete TINYINT(1) NOT NULL, pilote VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_conformite_organisation_processus (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(255) NOT NULL, couleur VARCHAR(255) NOT NULL, description VARCHAR(512) DEFAULT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_conformite_organisation_reponse (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', question_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', conformite_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', reponse INT DEFAULT NULL, reponse_raison VARCHAR(255) DEFAULT NULL, INDEX IDX_DDCDACB71E27F6BF (question_id), INDEX IDX_DDCDACB794F3C51 (conformite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_conformite_organisation_conformite (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', processus_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', conformite INT DEFAULT NULL, INDEX IDX_7BD97140A55629DC (processus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conformite_organisation_conformite_action_protection (conformite_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesurement_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_2B0F83F94F3C51 (conformite_id), INDEX IDX_2B0F83F2EA38911 (mesurement_id), PRIMARY KEY(conformite_id, mesurement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_conformite_organisation_question (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', processus_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', nom VARCHAR(512) NOT NULL, position VARCHAR(255) NOT NULL, INDEX IDX_E6704300A55629DC (processus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_conformite_organisation_participant ADD CONSTRAINT FK_24A83582456C5646 FOREIGN KEY (evaluation_id) REFERENCES registry_conformite_organisation_evaluation (id)');
        $this->addSql('ALTER TABLE registry_conformite_organisation_reponse ADD CONSTRAINT FK_DDCDACB71E27F6BF FOREIGN KEY (question_id) REFERENCES registry_conformite_organisation_question (id)');
        $this->addSql('ALTER TABLE registry_conformite_organisation_reponse ADD CONSTRAINT FK_DDCDACB794F3C51 FOREIGN KEY (conformite_id) REFERENCES registry_conformite_organisation_conformite (id)');
        $this->addSql('ALTER TABLE registry_conformite_organisation_conformite ADD CONSTRAINT FK_7BD97140A55629DC FOREIGN KEY (processus_id) REFERENCES registry_conformite_organisation_processus (id)');
        $this->addSql('ALTER TABLE conformite_organisation_conformite_action_protection ADD CONSTRAINT FK_2B0F83F94F3C51 FOREIGN KEY (conformite_id) REFERENCES registry_conformite_organisation_conformite (id)');
        $this->addSql('ALTER TABLE conformite_organisation_conformite_action_protection ADD CONSTRAINT FK_2B0F83F2EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id)');
        $this->addSql('ALTER TABLE registry_conformite_organisation_question ADD CONSTRAINT FK_E6704300A55629DC FOREIGN KEY (processus_id) REFERENCES registry_conformite_organisation_processus (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_conformite_organisation_participant DROP FOREIGN KEY FK_24A83582456C5646');
        $this->addSql('ALTER TABLE registry_conformite_organisation_conformite DROP FOREIGN KEY FK_7BD97140A55629DC');
        $this->addSql('ALTER TABLE registry_conformite_organisation_question DROP FOREIGN KEY FK_E6704300A55629DC');
        $this->addSql('ALTER TABLE registry_conformite_organisation_reponse DROP FOREIGN KEY FK_DDCDACB794F3C51');
        $this->addSql('ALTER TABLE conformite_organisation_conformite_action_protection DROP FOREIGN KEY FK_2B0F83F94F3C51');
        $this->addSql('ALTER TABLE registry_conformite_organisation_reponse DROP FOREIGN KEY FK_DDCDACB71E27F6BF');
        $this->addSql('DROP TABLE registry_conformite_organisation_participant');
        $this->addSql('DROP TABLE registry_conformite_organisation_evaluation');
        $this->addSql('DROP TABLE registry_conformite_organisation_processus');
        $this->addSql('DROP TABLE registry_conformite_organisation_reponse');
        $this->addSql('DROP TABLE registry_conformite_organisation_conformite');
        $this->addSql('DROP TABLE conformite_organisation_conformite_action_protection');
        $this->addSql('DROP TABLE registry_conformite_organisation_question');
    }
}
