<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220224090640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_mesurement ADD treatment_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD contractor_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD request_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ADD violation_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE collectivity_id collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE cloned_from_id cloned_from_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE cost cost VARCHAR(255) DEFAULT NULL, CHANGE charge charge VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE planification_date planification_date DATE DEFAULT NULL, CHANGE comment comment VARCHAR(255) DEFAULT NULL, CHANGE priority priority VARCHAR(255) DEFAULT NULL, CHANGE manager manager VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFA471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id)');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFAB0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id)');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFA427EB8A5 FOREIGN KEY (request_id) REFERENCES registry_request (id)');
        $this->addSql('ALTER TABLE registry_mesurement ADD CONSTRAINT FK_9CFD1BFA7386118A FOREIGN KEY (violation_id) REFERENCES registry_violation (id)');
        $this->addSql('CREATE INDEX IDX_9CFD1BFA471C0366 ON registry_mesurement (treatment_id)');
        $this->addSql('CREATE INDEX IDX_9CFD1BFAB0265DC7 ON registry_mesurement (contractor_id)');
        $this->addSql('CREATE INDEX IDX_9CFD1BFA427EB8A5 ON registry_mesurement (request_id)');
        $this->addSql('CREATE INDEX IDX_9CFD1BFA7386118A ON registry_mesurement (violation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFA471C0366');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFAB0265DC7');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFA427EB8A5');
        $this->addSql('ALTER TABLE registry_mesurement DROP FOREIGN KEY FK_9CFD1BFA7386118A');
        $this->addSql('DROP INDEX IDX_9CFD1BFA471C0366 ON registry_mesurement');
        $this->addSql('DROP INDEX IDX_9CFD1BFAB0265DC7 ON registry_mesurement');
        $this->addSql('DROP INDEX IDX_9CFD1BFA427EB8A5 ON registry_mesurement');
        $this->addSql('DROP INDEX IDX_9CFD1BFA7386118A ON registry_mesurement');
        $this->addSql('ALTER TABLE registry_mesurement DROP treatment_id, DROP contractor_id, DROP request_id, DROP violation_id, CHANGE cloned_from_id cloned_from_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE collectivity_id collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE type type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE cost cost VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE charge charge VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE planification_date planification_date DATE DEFAULT \'NULL\', CHANGE comment comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE priority priority VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE manager manager VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
