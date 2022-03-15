<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220315121900 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mesurement_contractor (contractor_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesurement_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_A548F653B0265DC7 (contractor_id), INDEX IDX_A548F6532EA38911 (mesurement_id), PRIMARY KEY(contractor_id, mesurement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesurement_request (request_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesurement_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_F07A4E10427EB8A5 (request_id), INDEX IDX_F07A4E102EA38911 (mesurement_id), PRIMARY KEY(request_id, mesurement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesurement_treatment (treatment_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesurement_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_C879817E471C0366 (treatment_id), INDEX IDX_C879817E2EA38911 (mesurement_id), PRIMARY KEY(treatment_id, mesurement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesurement_violation (violation_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesurement_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_B7C2F9AD7386118A (violation_id), INDEX IDX_B7C2F9AD2EA38911 (mesurement_id), PRIMARY KEY(violation_id, mesurement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mesurement_contractor ADD CONSTRAINT FK_A548F653B0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesurement_contractor ADD CONSTRAINT FK_A548F6532EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesurement_request ADD CONSTRAINT FK_F07A4E10427EB8A5 FOREIGN KEY (request_id) REFERENCES registry_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesurement_request ADD CONSTRAINT FK_F07A4E102EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesurement_treatment ADD CONSTRAINT FK_C879817E471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesurement_treatment ADD CONSTRAINT FK_C879817E2EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesurement_violation ADD CONSTRAINT FK_B7C2F9AD7386118A FOREIGN KEY (violation_id) REFERENCES registry_violation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesurement_violation ADD CONSTRAINT FK_B7C2F9AD2EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFA427EB8A5');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFA471C0366');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFA7386118A');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFAB0265DC7');
        $this->addSql('DROP INDEX IDX_9CFD1BFA471C0366 ON registry_mesurement');
        $this->addSql('DROP INDEX IDX_9CFD1BFAB0265DC7 ON registry_mesurement');
        $this->addSql('DROP INDEX IDX_9CFD1BFA427EB8A5 ON registry_mesurement');
        $this->addSql('DROP INDEX IDX_9CFD1BFA7386118A ON registry_mesurement');
        $this->addSql('ALTER TABLE registry_mesurement DROP treatment_id, DROP contractor_id, DROP request_id, DROP violation_id, CHANGE collectivity_id collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE cloned_from_id cloned_from_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE cost cost VARCHAR(255) DEFAULT NULL, CHANGE charge charge VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE planification_date planification_date DATE DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT NULL, CHANGE priority priority VARCHAR(255) DEFAULT NULL, CHANGE manager manager VARCHAR(255) DEFAULT NULL');
 }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mesurement_contractor');
        $this->addSql('DROP TABLE mesurement_request');
        $this->addSql('DROP TABLE mesurement_treatment');
        $this->addSql('DROP TABLE mesurement_violation');
        $this->addSql('ALTER TABLE registry_mesurement ADD treatment_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', ADD contractor_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', ADD request_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', ADD violation_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE cloned_from_id cloned_from_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE collectivity_id collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE type type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE cost cost VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE charge charge VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE planification_date planification_date DATE DEFAULT \'NULL\', CHANGE comment comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE priority priority VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE manager manager VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFA427EB8A5 FOREIGN KEY (request_id) REFERENCES registry_request (id)');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFA471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id)');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFA7386118A FOREIGN KEY (violation_id) REFERENCES registry_violation (id)');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFAB0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id)');
        $this->addSql('CREATE INDEX IDX_9CFD1BFA471C0366 ON registry_mesurement (treatment_id)');
        $this->addSql('CREATE INDEX IDX_9CFD1BFAB0265DC7 ON registry_mesurement (contractor_id)');
        $this->addSql('CREATE INDEX IDX_9CFD1BFA427EB8A5 ON registry_mesurement (request_id)');
        $this->addSql('CREATE INDEX IDX_9CFD1BFA7386118A ON registry_mesurement (violation_id)');
}
}
