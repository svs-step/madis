<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220414080347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notification_mail_parameters (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', is_notified TINYINT(1) NOT NULL, frequency JSON NOT NULL COMMENT \'(DC2Type:json_array)\', interval_hours INT NOT NULL, start_week JSON NOT NULL COMMENT \'(DC2Type:json_array)\', start_day JSON NOT NULL COMMENT \'(DC2Type:json_array)\', start_hour INT NOT NULL, is_treatment TINYINT(1) NOT NULL, is_subcontract TINYINT(1) NOT NULL, is_request TINYINT(1) NOT NULL, is_violation TINYINT(1) NOT NULL, is_proof TINYINT(1) NOT NULL, is_protect_action TINYINT(1) NOT NULL, is_maturity TINYINT(1) NOT NULL, is_treatmen_conformity TINYINT(1) NOT NULL, is_organization_conformity TINYINT(1) NOT NULL, is_aipd TINYINT(1) NOT NULL, is_document TINYINT(1) NOT NULL, last_notif_send DATETIME DEFAULT NULL, INDEX IDX_B3D9B319A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification_mail_parameters ADD CONSTRAINT FK_B3D9B319A76ED395 FOREIGN KEY (user_id) REFERENCES user_user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE notification_mail_parameters');
    }
}
