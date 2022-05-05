<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220505125352 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_notification (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', is_notified TINYINT(1) NOT NULL, frequency LONGTEXT DEFAULT NULL, interval_hours INT DEFAULT NULL, start_week LONGTEXT DEFAULT NULL, start_day LONGTEXT DEFAULT NULL, start_hour VARCHAR(255) DEFAULT NULL, is_treatment TINYINT(1) DEFAULT NULL, is_subcontract TINYINT(1) DEFAULT NULL, is_request TINYINT(1) DEFAULT NULL, is_violation TINYINT(1) DEFAULT NULL, is_proof TINYINT(1) DEFAULT NULL, is_protectAction TINYINT(1) DEFAULT NULL, is_maturity TINYINT(1) DEFAULT NULL, is_treatmenConformity TINYINT(1) DEFAULT NULL, is_organizationConformity TINYINT(1) DEFAULT NULL, is_AIPD TINYINT(1) DEFAULT NULL, is_document TINYINT(1) DEFAULT NULL, last_notif_send DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE notification_mail_parameters');
        $this->addSql('ALTER TABLE notification_user CHANGE notif_id notif_id VARCHAR(255) NOT NULL, CHANGE user_id user_id VARCHAR(255) DEFAULT NULL, CHANGE token token VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_user ADD notification_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE collectivity_id collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE forget_password_token forget_password_token VARCHAR(255) DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE last_login last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE api_authorized api_authorized TINYINT(1) DEFAULT NULL, CHANGE document_view document_view TINYINT(1) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE moreInfos moreInfos JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80EF1A9D84 FOREIGN KEY (notification_id) REFERENCES user_notification (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7129A80EF1A9D84 ON user_user (notification_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80EF1A9D84');
        $this->addSql('CREATE TABLE notification_mail_parameters (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', is_notified TINYINT(1) NOT NULL, frequency JSON CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', interval_hours INT NOT NULL, start_week JSON CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', start_day JSON CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', start_hour INT NOT NULL, is_treatment TINYINT(1) NOT NULL, is_subcontract TINYINT(1) NOT NULL, is_request TINYINT(1) NOT NULL, is_violation TINYINT(1) NOT NULL, is_proof TINYINT(1) NOT NULL, is_protect_action TINYINT(1) NOT NULL, is_maturity TINYINT(1) NOT NULL, is_treatmen_conformity TINYINT(1) NOT NULL, is_organization_conformity TINYINT(1) NOT NULL, is_aipd TINYINT(1) NOT NULL, is_document TINYINT(1) NOT NULL, last_notif_send DATETIME DEFAULT \'NULL\', INDEX IDX_B3D9B319A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE notification_mail_parameters ADD CONSTRAINT FK_B3D9B319A76ED395 FOREIGN KEY (user_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE user_notification');
        $this->addSql('ALTER TABLE notification_user CHANGE notif_id notif_id INT NOT NULL, CHANGE user_id user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE token token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX UNIQ_F7129A80EF1A9D84 ON user_user');
        $this->addSql('ALTER TABLE user_user DROP notification_id, CHANGE collectivity_id collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE moreInfos moreInfos JSON CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_bin` COMMENT \'(DC2Type:json_array)\', CHANGE forget_password_token forget_password_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE last_login last_login DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE api_authorized api_authorized TINYINT(1) DEFAULT \'NULL\', CHANGE document_view document_view TINYINT(1) DEFAULT \'NULL\'');
    }
}
