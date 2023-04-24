<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230421062109 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Replace departmental_union type to syndicat and replace "m " to "m" for legal_civility for collectivity;';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE user_collectivity SET type="syndicat" WHERE type="departmental_union"');
        $this->addSql('UPDATE user_collectivity SET legal_manager_civility = "m" WHERE legal_manager_civility = "m "');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
