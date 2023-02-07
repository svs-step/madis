<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408082933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesurement_treatment DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_treatment ADD PRIMARY KEY (mesurement_id, treatment_id)');
        $this->addSql('ALTER TABLE mesurement_contractor DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_contractor ADD PRIMARY KEY (mesurement_id, contractor_id)');
        $this->addSql('ALTER TABLE mesurement_request DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_request ADD PRIMARY KEY (mesurement_id, request_id)');
        $this->addSql('ALTER TABLE mesurement_violation DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_violation ADD PRIMARY KEY (mesurement_id, violation_id)');
        $this->addSql('ALTER TABLE user_user ADD refOp TINYINT(1) DEFAULT NULL, ADD respInfo TINYINT(1) DEFAULT NULL, ADD respTreat TINYINT(1) DEFAULT NULL, ADD dpo TINYINT(1) DEFAULT NULL, CHANGE collectivity_id collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE forget_password_token forget_password_token VARCHAR(255) DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE last_login last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE api_authorized api_authorized TINYINT(1) DEFAULT NULL, CHANGE document_view document_view TINYINT(1) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesurement_contractor DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_contractor ADD PRIMARY KEY (contractor_id, mesurement_id)');
        $this->addSql('ALTER TABLE mesurement_request DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_request ADD PRIMARY KEY (request_id, mesurement_id)');
        $this->addSql('ALTER TABLE mesurement_treatment DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_treatment ADD PRIMARY KEY (treatment_id, mesurement_id)');
        $this->addSql('ALTER TABLE mesurement_violation DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesurement_violation ADD PRIMARY KEY (violation_id, mesurement_id)');
        $this->addSql('ALTER TABLE user_user DROP refOp, DROP respInfo, DROP respTreat, DROP dpo, CHANGE collectivity_id collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE forget_password_token forget_password_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE last_login last_login DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE api_authorized api_authorized TINYINT(1) DEFAULT \'NULL\', CHANGE document_view document_view TINYINT(1) DEFAULT \'NULL\'');
    }
}
