<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180708183305 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment ADD data_category_other LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE registry_treatment SET data_category_other=data_category');
        $this->addSql('ALTER TABLE registry_treatment DROP security_encryptioncomment, CHANGE data_category data_category JSON NOT NULL COMMENT \'(DC2Type:json_array)\', CHANGE security_encryptioncheck paper_processing TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment ADD security_encryptioncomment VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP data_category_other, CHANGE data_category data_category LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE paper_processing security_encryptioncheck TINYINT(1) NOT NULL');
    }
}
