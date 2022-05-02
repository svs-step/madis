<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220222130548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_treatment_request (treatment_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', request_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_D83DF45F471C0366 (treatment_id), INDEX IDX_D83DF45F427EB8A5 (request_id), PRIMARY KEY(treatment_id, request_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registry_treatment_violation (treatment_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', violation_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_EB97D8B2471C0366 (treatment_id), INDEX IDX_EB97D8B27386118A (violation_id), PRIMARY KEY(treatment_id, violation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_treatment_request ADD CONSTRAINT FK_D83DF45F471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_treatment_request ADD CONSTRAINT FK_D83DF45F427EB8A5 FOREIGN KEY (request_id) REFERENCES registry_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_treatment_violation ADD CONSTRAINT FK_EB97D8B2471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_treatment_violation ADD CONSTRAINT FK_EB97D8B27386118A FOREIGN KEY (violation_id) REFERENCES registry_violation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE registry_treatment_request');
        $this->addSql('DROP TABLE registry_treatment_violation');
    }
}
