<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180619192219 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_contractor (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, referent VARCHAR(255) NOT NULL, contractual_clauses_verified TINYINT(1) NOT NULL, conform TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', address_line_one VARCHAR(255) NOT NULL, address_line_two VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) NOT NULL, address_zip_code INT NOT NULL, address_mail VARCHAR(255) NOT NULL, address_phone_number VARCHAR(10) DEFAULT NULL, INDEX IDX_AE100259BD56F776 (collectivity_id), INDEX IDX_AE10025961220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_treatment (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, goal LONGTEXT NOT NULL, software VARCHAR(255) DEFAULT NULL, legal_basis JSON NOT NULL COMMENT \'(DC2Type:json_array)\', legal_basis_justification LONGTEXT DEFAULT NULL, concerned_people JSON NOT NULL COMMENT \'(DC2Type:json_array)\', data_category LONGTEXT NOT NULL, sensible_informations TINYINT(1) NOT NULL, recipient_category LONGTEXT NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delay_number VARCHAR(255) DEFAULT NULL, delay_period VARCHAR(255) DEFAULT NULL, INDEX IDX_4B52AAB1BD56F776 (collectivity_id), INDEX IDX_4B52AAB161220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE treatment_contractor (treatment_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', contractor_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_50056FFA471C0366 (treatment_id), INDEX IDX_50056FFAB0265DC7 (contractor_id), PRIMARY KEY(treatment_id, contractor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_collectivity (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(80) NOT NULL, short_name VARCHAR(80) NOT NULL, type VARCHAR(255) NOT NULL, siren INT NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', address_line_one VARCHAR(255) NOT NULL, address_line_two VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) NOT NULL, address_zip_code INT NOT NULL, address_insee VARCHAR(255) NOT NULL, legal_manager_civility VARCHAR(255) DEFAULT NULL, legal_manager_first_name VARCHAR(255) DEFAULT NULL, legal_manager_last_name VARCHAR(255) DEFAULT NULL, legal_manager_job VARCHAR(255) DEFAULT NULL, legal_manager_mail VARCHAR(255) DEFAULT NULL, legal_manager_phone_number VARCHAR(10) DEFAULT NULL, referent_civility VARCHAR(255) DEFAULT NULL, referent_first_name VARCHAR(255) DEFAULT NULL, referent_last_name VARCHAR(255) DEFAULT NULL, referent_job VARCHAR(255) DEFAULT NULL, referent_mail VARCHAR(255) DEFAULT NULL, referent_phone_number VARCHAR(10) DEFAULT NULL, dpo_civility VARCHAR(255) DEFAULT NULL, dpo_first_name VARCHAR(255) DEFAULT NULL, dpo_last_name VARCHAR(255) DEFAULT NULL, dpo_job VARCHAR(255) DEFAULT NULL, dpo_mail VARCHAR(255) DEFAULT NULL, dpo_phone_number VARCHAR(10) DEFAULT NULL, it_manager_civility VARCHAR(255) DEFAULT NULL, it_manager_first_name VARCHAR(255) DEFAULT NULL, it_manager_last_name VARCHAR(255) DEFAULT NULL, it_manager_job VARCHAR(255) DEFAULT NULL, it_manager_mail VARCHAR(255) DEFAULT NULL, it_manager_phone_number VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json_array)\', enabled TINYINT(1) NOT NULL, forget_password_token VARCHAR(255) DEFAULT NULL, INDEX IDX_F7129A80BD56F776 (collectivity_id), UNIQUE INDEX UNIQ_F7129A80E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_contractor ADD CONSTRAINT FK_AE100259BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_contractor ADD CONSTRAINT FK_AE10025961220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB1BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB161220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
        $this->addSql('ALTER TABLE treatment_contractor ADD CONSTRAINT FK_50056FFA471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id)');
        $this->addSql('ALTER TABLE treatment_contractor ADD CONSTRAINT FK_50056FFAB0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE treatment_contractor DROP FOREIGN KEY FK_50056FFAB0265DC7');
        $this->addSql('ALTER TABLE treatment_contractor DROP FOREIGN KEY FK_50056FFA471C0366');
        $this->addSql('ALTER TABLE registry_contractor DROP FOREIGN KEY FK_AE100259BD56F776');
        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB1BD56F776');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80BD56F776');
        $this->addSql('ALTER TABLE registry_contractor DROP FOREIGN KEY FK_AE10025961220EA6');
        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB161220EA6');
        $this->addSql('DROP TABLE registry_contractor');
        $this->addSql('DROP TABLE registry_treatment');
        $this->addSql('DROP TABLE treatment_contractor');
        $this->addSql('DROP TABLE user_collectivity');
        $this->addSql('DROP TABLE user_user');
    }
}
