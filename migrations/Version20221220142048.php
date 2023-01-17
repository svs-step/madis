<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221220142048 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_collectivity ADD other_type LONGTEXT DEFAULT NULL, ADD finess_geo VARCHAR(255) DEFAULT NULL, CHANGE address_line_two address_line_two VARCHAR(255) DEFAULT NULL, CHANGE legal_manager_civility legal_manager_civility VARCHAR(255) DEFAULT NULL, CHANGE legal_manager_first_name legal_manager_first_name VARCHAR(255) DEFAULT NULL, CHANGE legal_manager_last_name legal_manager_last_name VARCHAR(255) DEFAULT NULL, CHANGE legal_manager_job legal_manager_job VARCHAR(255) DEFAULT NULL, CHANGE legal_manager_mail legal_manager_mail VARCHAR(255) DEFAULT NULL, CHANGE legal_manager_phone_number legal_manager_phone_number VARCHAR(10) DEFAULT NULL, CHANGE referent_civility referent_civility VARCHAR(255) DEFAULT NULL, CHANGE referent_first_name referent_first_name VARCHAR(255) DEFAULT NULL, CHANGE referent_last_name referent_last_name VARCHAR(255) DEFAULT NULL, CHANGE referent_job referent_job VARCHAR(255) DEFAULT NULL, CHANGE referent_mail referent_mail VARCHAR(255) DEFAULT NULL, CHANGE referent_phone_number referent_phone_number VARCHAR(10) DEFAULT NULL, CHANGE dpo_civility dpo_civility VARCHAR(255) DEFAULT NULL, CHANGE dpo_first_name dpo_first_name VARCHAR(255) DEFAULT NULL, CHANGE dpo_last_name dpo_last_name VARCHAR(255) DEFAULT NULL, CHANGE dpo_job dpo_job VARCHAR(255) DEFAULT NULL, CHANGE dpo_mail dpo_mail VARCHAR(255) DEFAULT NULL, CHANGE dpo_phone_number dpo_phone_number VARCHAR(10) DEFAULT NULL, CHANGE it_manager_civility it_manager_civility VARCHAR(255) DEFAULT NULL, CHANGE it_manager_first_name it_manager_first_name VARCHAR(255) DEFAULT NULL, CHANGE it_manager_last_name it_manager_last_name VARCHAR(255) DEFAULT NULL, CHANGE it_manager_job it_manager_job VARCHAR(255) DEFAULT NULL, CHANGE it_manager_mail it_manager_mail VARCHAR(255) DEFAULT NULL, CHANGE it_manager_phone_number it_manager_phone_number VARCHAR(10) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE is_services_enabled is_services_enabled TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_collectivity DROP other_type, CHANGE website website VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE is_services_enabled is_services_enabled TINYINT(1) DEFAULT \'NULL\', CHANGE address_line_two address_line_two VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE legal_manager_civility legal_manager_civility VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE legal_manager_first_name legal_manager_first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE legal_manager_last_name legal_manager_last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE legal_manager_job legal_manager_job VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE legal_manager_mail legal_manager_mail VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE legal_manager_phone_number legal_manager_phone_number VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE referent_civility referent_civility VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE referent_first_name referent_first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE referent_last_name referent_last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE referent_job referent_job VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE referent_mail referent_mail VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE referent_phone_number referent_phone_number VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE dpo_civility dpo_civility VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE dpo_first_name dpo_first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE dpo_last_name dpo_last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE dpo_job dpo_job VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE dpo_mail dpo_mail VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE dpo_phone_number dpo_phone_number VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE it_manager_civility it_manager_civility VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE it_manager_first_name it_manager_first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE it_manager_last_name it_manager_last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE it_manager_job it_manager_job VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE it_manager_mail it_manager_mail VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE it_manager_phone_number it_manager_phone_number VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
