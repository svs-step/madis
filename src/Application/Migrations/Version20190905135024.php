<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190905135024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin_duplication (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', source_collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(50) NOT NULL, data_ids JSON NOT NULL COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_AE3FF3CE1B49D97D (source_collectivity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_duplication_collectivity (duplication_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_59EF3717A78FD7B3 (duplication_id), INDEX IDX_59EF3717BD56F776 (collectivity_id), PRIMARY KEY(duplication_id, collectivity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_duplication ADD CONSTRAINT FK_AE3FF3CE1B49D97D FOREIGN KEY (source_collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE admin_duplication_collectivity ADD CONSTRAINT FK_59EF3717A78FD7B3 FOREIGN KEY (duplication_id) REFERENCES admin_duplication (id)');
        $this->addSql('ALTER TABLE admin_duplication_collectivity ADD CONSTRAINT FK_59EF3717BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
        $this->addSql('ALTER TABLE registry_mesurement ADD cloned_from_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFAB2CF0654 FOREIGN KEY (cloned_from_id) REFERENCES registry_mesurement (id)');
        $this->addSql('CREATE INDEX IDX_9CFD1BFAB2CF0654 ON registry_mesurement (cloned_from_id)');
        $this->addSql('ALTER TABLE registry_contractor ADD cloned_from_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_contractor ADD CONSTRAINT FK_AE100259B2CF0654 FOREIGN KEY (cloned_from_id) REFERENCES registry_contractor (id)');
        $this->addSql('CREATE INDEX IDX_AE100259B2CF0654 ON registry_contractor (cloned_from_id)');
        $this->addSql('ALTER TABLE registry_treatment ADD cloned_from_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB1B2CF0654 FOREIGN KEY (cloned_from_id) REFERENCES registry_treatment (id)');
        $this->addSql('CREATE INDEX IDX_4B52AAB1B2CF0654 ON registry_treatment (cloned_from_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_duplication_collectivity DROP FOREIGN KEY FK_59EF3717A78FD7B3');
        $this->addSql('DROP TABLE admin_duplication');
        $this->addSql('DROP TABLE admin_duplication_collectivity');
        $this->addSql('ALTER TABLE registry_contractor DROP FOREIGN KEY FK_AE100259B2CF0654');
        $this->addSql('DROP INDEX IDX_AE100259B2CF0654 ON registry_contractor');
        $this->addSql('ALTER TABLE registry_contractor DROP cloned_from_id');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFAB2CF0654');
        $this->addSql('DROP INDEX IDX_9CFD1BFAB2CF0654 ON registry_mesurement');
        $this->addSql('ALTER TABLE registry_mesurement DROP cloned_from_id');
        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB1B2CF0654');
        $this->addSql('DROP INDEX IDX_4B52AAB1B2CF0654 ON registry_treatment');
        $this->addSql('ALTER TABLE registry_treatment DROP cloned_from_id');
    }
}
