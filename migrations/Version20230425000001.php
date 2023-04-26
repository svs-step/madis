<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use DoctrineExtensions\Query\Mysql\Date;
use DoctrineExtensions\Query\Mysql\Now;
use Ramsey\Uuid\Uuid;

final class Version20230425000001 extends AbstractMigration
{

    public function getDescription(): string
    {
        return 'add category violation';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('INSERT INTO category(id, name, systeme, created_at, updated_at) VALUES (?, ?, ?, ?, ?)',[Uuid::uuid4(),"Violation",true, date_create()->format('Y-m-d H:i:s'), date_create()->format('Y-m-d H:i:s')]);
        //$this->addSql('INSERT INTO conformite_traitement_question(id, question, position) VALUES (?, ?, ?)', [Uuid::uuid4(), $item['question'], $item['position']]);


    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }

}
