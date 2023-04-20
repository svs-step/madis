<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307103543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE user_user ADD not_generates_notifications TINYINT(1) DEFAULT NULL');
        // Set generates_notifications to true for all users
        $this->addSql('UPDATE user_user set not_generates_notifications=0');
        // Set generates_notifications to false for user with role admin
        $this->addSql('UPDATE user_user set not_generates_notifications=1 WHERE roles LIKE "%ROLE_ADMIN%"');

        // Set notification preference to all active. https://gitlab.adullact.net/soluris/madis/-/issues/632
        $this->addSql('UPDATE user_notification_preference set notification_mask=2047');

        // Set moreinfo to DPD for existing users ["MOREINFO_DPD"]
        $this->addSql('UPDATE user_user set moreInfos=\'' . json_encode(['MOREINFO_DPD']) . '\' WHERE roles LIKE "%ROLE_ADMIN%"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE user_user DROP not_generates_notifications');
    }
}
