<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use DoctrineExtensions\Query\Mysql\Date;
use DoctrineExtensions\Query\Mysql\Now;
use Ramsey\Uuid\Uuid;

final class Version20230502000001 extends AbstractMigration
{

    public function getDescription(): string
    {
        return 'Change document category name';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE category SET name=? WHERE name="Conformité de l\'organisation"',["Conformité de la structure"]);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }

}
