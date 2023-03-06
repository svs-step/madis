<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306132134 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registry_treatment_shelf_life (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', treatment_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) DEFAULT NULL, duration VARCHAR(255) DEFAULT NULL, ultimate_fate VARCHAR(255) DEFAULT NULL, INDEX IDX_FA52D4C8471C0366 (treatment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registry_treatment_shelf_life ADD CONSTRAINT FK_FA52D4C8471C0366 FOREIGN KEY (treatment_id) REFERENCES registry_treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registry_treatment DROP ultimate_fate');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE registry_treatment_shelf_life');
        $this->addSql('ALTER TABLE registry_treatment ADD ultimate_fate VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
