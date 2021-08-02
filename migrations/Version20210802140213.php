<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210802140213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_service_user (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', service_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_779C5424A76ED395 (user_id), INDEX IDX_779C5424ED5CA9E6 (service_id), PRIMARY KEY(user_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_service (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E6D62580BD56F776 (collectivity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_service_user ADD CONSTRAINT FK_779C5424A76ED395 FOREIGN KEY (user_id) REFERENCES user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_service_user ADD CONSTRAINT FK_779C5424ED5CA9E6 FOREIGN KEY (service_id) REFERENCES registry_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_service ADD CONSTRAINT FK_E6D62580BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_treatment ADD service_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB1ED5CA9E6 FOREIGN KEY (service_id) REFERENCES registry_service (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4B52AAB1ED5CA9E6 ON registry_treatment (service_id)');
        $this->addSql('ALTER TABLE registry_request ADD service_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_request ADD CONSTRAINT FK_3CDC30CDED5CA9E6 FOREIGN KEY (service_id) REFERENCES registry_service (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_3CDC30CDED5CA9E6 ON registry_request (service_id)');
        $this->addSql('ALTER TABLE registry_contractor ADD service_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_contractor ADD CONSTRAINT FK_AE100259ED5CA9E6 FOREIGN KEY (service_id) REFERENCES registry_service (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_AE100259ED5CA9E6 ON registry_contractor (service_id)');
        $this->addSql('ALTER TABLE registry_violation ADD service_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_violation ADD CONSTRAINT FK_34E9D262ED5CA9E6 FOREIGN KEY (service_id) REFERENCES registry_service (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_34E9D262ED5CA9E6 ON registry_violation (service_id)');
        $this->addSql('ALTER TABLE user_collectivity ADD is_services_enabled TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB1ED5CA9E6');
        $this->addSql('ALTER TABLE registry_request DROP FOREIGN KEY FK_3CDC30CDED5CA9E6');
        $this->addSql('ALTER TABLE registry_contractor DROP FOREIGN KEY FK_AE100259ED5CA9E6');
        $this->addSql('ALTER TABLE registry_violation DROP FOREIGN KEY FK_34E9D262ED5CA9E6');
        $this->addSql('ALTER TABLE registry_service_user DROP FOREIGN KEY FK_779C5424ED5CA9E6');
        $this->addSql('DROP TABLE registry_service_user');
        $this->addSql('DROP TABLE registry_service');
        $this->addSql('DROP INDEX IDX_AE100259ED5CA9E6 ON registry_contractor');
        $this->addSql('ALTER TABLE registry_contractor DROP service_id');
        $this->addSql('DROP INDEX IDX_3CDC30CDED5CA9E6 ON registry_request');
        $this->addSql('ALTER TABLE registry_request DROP service_id');
        $this->addSql('DROP INDEX IDX_4B52AAB1ED5CA9E6 ON registry_treatment');
        $this->addSql('ALTER TABLE registry_treatment DROP service_id');
        $this->addSql('DROP INDEX IDX_34E9D262ED5CA9E6 ON registry_violation');
        $this->addSql('ALTER TABLE registry_violation DROP service_id');
        $this->addSql('ALTER TABLE user_collectivity DROP is_services_enabled');
    }
}
