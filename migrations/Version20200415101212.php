<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200415101212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_contractor ADD adopted_security_features TINYINT(1) NOT NULL, ADD maintains_treatment_register TINYINT(1) NOT NULL, ADD sending_data_outside_eu TINYINT(1) NOT NULL, ADD dpo_civility VARCHAR(255) DEFAULT NULL, ADD dpo_first_name VARCHAR(255) DEFAULT NULL, ADD dpo_last_name VARCHAR(255) DEFAULT NULL, ADD dpo_job VARCHAR(255) DEFAULT NULL, ADD dpo_mail VARCHAR(255) DEFAULT NULL, ADD dpo_phone_number VARCHAR(10) DEFAULT NULL, ADD legal_manager_civility VARCHAR(255) DEFAULT NULL, ADD legal_manager_first_name VARCHAR(255) DEFAULT NULL, ADD legal_manager_last_name VARCHAR(255) DEFAULT NULL, ADD legal_manager_job VARCHAR(255) DEFAULT NULL, ADD legal_manager_mail VARCHAR(255) DEFAULT NULL, ADD legal_manager_phone_number VARCHAR(10) DEFAULT NULL, DROP conform');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_contractor ADD conform TINYINT(1) NOT NULL, DROP adopted_security_features, DROP maintains_treatment_register, DROP sending_data_outside_eu, DROP dpo_civility, DROP dpo_first_name, DROP dpo_last_name, DROP dpo_job, DROP dpo_mail, DROP dpo_phone_number, DROP legal_manager_civility, DROP legal_manager_first_name, DROP legal_manager_last_name, DROP legal_manager_job, DROP legal_manager_mail, DROP legal_manager_phone_number');
    }
}
