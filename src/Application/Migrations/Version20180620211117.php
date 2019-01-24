<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180620211117 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment CHANGE goal goal LONGTEXT DEFAULT NULL, CHANGE data_category data_category LONGTEXT DEFAULT NULL, CHANGE recipient_category recipient_category LONGTEXT DEFAULT NULL, CHANGE delay_number delay_number INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment CHANGE goal goal LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE data_category data_category LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE recipient_category recipient_category LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE delay_number delay_number VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
