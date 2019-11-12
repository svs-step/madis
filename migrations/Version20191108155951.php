<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191108155951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB1B2CF0654');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB1B2CF0654 FOREIGN KEY (cloned_from_id) REFERENCES registry_treatment (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment DROP FOREIGN KEY FK_4B52AAB1B2CF0654');
        $this->addSql('ALTER TABLE registry_treatment ADD CONSTRAINT FK_4B52AAB1B2CF0654 FOREIGN KEY (cloned_from_id) REFERENCES registry_treatment (id)');
    }
}
