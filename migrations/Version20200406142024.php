<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200406142024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_comite_il_contact (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', contact_civility VARCHAR(255) DEFAULT NULL, contact_first_name VARCHAR(255) DEFAULT NULL, contact_last_name VARCHAR(255) DEFAULT NULL, contact_job VARCHAR(255) DEFAULT NULL, contact_mail VARCHAR(255) DEFAULT NULL, contact_phone_number VARCHAR(10) DEFAULT NULL, INDEX IDX_D1C84AE9BD56F776 (collectivity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_comite_il_contact ADD CONSTRAINT FK_D1C84AE9BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_comite_il_contact');
    }
}
