<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180810101618 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_violation (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', date DATE NOT NULL, in_progress TINYINT(1) NOT NULL, violation_nature VARCHAR(255) NOT NULL, origins JSON NOT NULL COMMENT \'(DC2Type:json_array)\', cause VARCHAR(255) NOT NULL, concerned_data_nature JSON NOT NULL COMMENT \'(DC2Type:json_array)\', concerned_people_categories JSON NOT NULL COMMENT \'(DC2Type:json_array)\', nb_affected_rows INT NOT NULL, nb_affected_persons INT NOT NULL, potential_impacts_nature JSON NOT NULL COMMENT \'(DC2Type:json_array)\', gravity VARCHAR(255) NOT NULL, communication VARCHAR(255) NOT NULL, communication_precision LONGTEXT DEFAULT NULL, applied_measures_after_violation LONGTEXT DEFAULT NULL, notification VARCHAR(255) NOT NULL, notification_details VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_34E9D262BD56F776 (collectivity_id), INDEX IDX_34E9D26261220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_violation ADD CONSTRAINT FK_34E9D262BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_violation ADD CONSTRAINT FK_34E9D26261220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE registry_violation');
    }
}
