<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731134234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add authorized_collectivity_types field to maturity_referentiel table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_referentiel ADD authorized_collectivity_types JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE maturity_referentiel_collectivity (referentiel_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_99DC2857805DB139 (referentiel_id), INDEX IDX_99DC2857BD56F776 (collectivity_id), PRIMARY KEY(referentiel_id, collectivity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE maturity_referentiel_collectivity ADD CONSTRAINT FK_99DC2857805DB139 FOREIGN KEY (referentiel_id) REFERENCES maturity_referentiel (id)');
        $this->addSql('ALTER TABLE maturity_referentiel_collectivity ADD CONSTRAINT FK_99DC2857BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_referentiel DROP authorized_collectivity_types');
        $this->addSql('DROP TABLE maturity_referentiel_collectivity');
    }
}
