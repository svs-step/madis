<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221223094240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_violation CHANGE violation_nature violation_natures JSON NOT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE contractor_violation (violation_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', contractor_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_D2E7389E7386118A (violation_id), INDEX IDX_D2E7389EB0265DC7 (contractor_id), PRIMARY KEY(violation_id, contractor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contractor_violation ADD CONSTRAINT FK_D2E7389E7386118A FOREIGN KEY (violation_id) REFERENCES registry_violation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contractor_violation ADD CONSTRAINT FK_D2E7389EB0265DC7 FOREIGN KEY (contractor_id) REFERENCES registry_contractor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE contractor_violation');
    }
}
