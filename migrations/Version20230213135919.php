<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213135919 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_tool (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, editor VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, prod_date DATE DEFAULT NULL, country_name VARCHAR(255) DEFAULT NULL, country_type VARCHAR(255) DEFAULT NULL, country_guarantees VARCHAR(255) DEFAULT NULL, other_info LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', archival_check TINYINT(1) NOT NULL, archival_comment VARCHAR(255) DEFAULT NULL, encrypted_check TINYINT(1) NOT NULL, encrypted_comment VARCHAR(255) DEFAULT NULL, access_control_check TINYINT(1) NOT NULL, access_control_comment VARCHAR(255) DEFAULT NULL, updating_check TINYINT(1) NOT NULL, updating_comment VARCHAR(255) DEFAULT NULL, backup_check TINYINT(1) NOT NULL, backup_comment VARCHAR(255) DEFAULT NULL, deletion_check TINYINT(1) NOT NULL, deletion_comment VARCHAR(255) DEFAULT NULL, tracking_check TINYINT(1) NOT NULL, tracking_comment VARCHAR(255) DEFAULT NULL, has_comment_check TINYINT(1) NOT NULL, has_comment_comment VARCHAR(255) DEFAULT NULL, other_check TINYINT(1) NOT NULL, other_comment VARCHAR(255) DEFAULT NULL, INDEX IDX_3C2A7CF261220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tool_proof (tool_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', proof_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_1A2C9A858F7B22CC (tool_id), INDEX IDX_1A2C9A85D7086615 (proof_id), PRIMARY KEY(tool_id, proof_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tool_treatment (tool_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', treatment_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_3DE4482E8F7B22CC (tool_id), INDEX IDX_3DE4482E471C0366 (treatment_id), PRIMARY KEY(tool_id, treatment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tool_contractor (tool_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', contractor_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_CED63A6E8F7B22CC (tool_id), INDEX IDX_CED63A6EB0265DC7 (contractor_id), PRIMARY KEY(tool_id, contractor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tool_mesurement (tool_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesurement_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_FC3B23CD8F7B22CC (tool_id), INDEX IDX_FC3B23CD2EA38911 (mesurement_id), PRIMARY KEY(tool_id, mesurement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_tool ADD CONSTRAINT FK_3C2A7CF261220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE tool_proof ADD CONSTRAINT FK_1A2C9A858F7B22CC FOREIGN KEY (tool_id) REFERENCES registry_tool (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tool_proof ADD CONSTRAINT FK_1A2C9A85D7086615 FOREIGN KEY (proof_id) REFERENCES registry_proof (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tool_treatment ADD CONSTRAINT FK_3DE4482E8F7B22CC FOREIGN KEY (tool_id) REFERENCES registry_tool (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tool_treatment ADD CONSTRAINT FK_3DE4482E471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tool_contractor ADD CONSTRAINT FK_CED63A6E8F7B22CC FOREIGN KEY (tool_id) REFERENCES registry_tool (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tool_contractor ADD CONSTRAINT FK_CED63A6EB0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tool_mesurement ADD CONSTRAINT FK_FC3B23CD8F7B22CC FOREIGN KEY (tool_id) REFERENCES registry_tool (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tool_mesurement ADD CONSTRAINT FK_FC3B23CD2EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tool_proof DROP FOREIGN KEY FK_1A2C9A858F7B22CC');
        $this->addSql('ALTER TABLE tool_treatment DROP FOREIGN KEY FK_3DE4482E8F7B22CC');
        $this->addSql('ALTER TABLE tool_contractor DROP FOREIGN KEY FK_CED63A6E8F7B22CC');
        $this->addSql('ALTER TABLE tool_mesurement DROP FOREIGN KEY FK_FC3B23CD8F7B22CC');
        $this->addSql('DROP TABLE registry_tool');
        $this->addSql('DROP TABLE tool_proof');
        $this->addSql('DROP TABLE tool_treatment');
        $this->addSql('DROP TABLE tool_contractor');
        $this->addSql('DROP TABLE tool_mesurement');
    }
}
