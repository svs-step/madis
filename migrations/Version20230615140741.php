<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230615140740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->skipIf(
            $schema->getTable('aipd_analyse_mesure_protection')->hasColumn('created_at'),
            'Skipping because `created_at` exists'
        );
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->skipIf(
            !$schema->getTable('aipd_analyse_mesure_protection')->hasColumn('created_at'),
            'Skipping because `created_at` does not exist'
        );
        $this->addSql('ALTER TABLE aipd_analyse_mesure_protection DROP created_at, DROP updated_at');
    }
}
