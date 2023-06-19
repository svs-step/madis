<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230619075239 extends AbstractMigration
{
    protected $oldquestions;

    public function getDescription() : string
    {
        return 'Set conformite traitement questions order';
    }

    public function preUp(Schema $schema): void
    {
        parent::preUp($schema);
        $this->oldquestions = $this->getData('SELECT * FROM conformite_traitement_question ORDER BY position ASC');
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        foreach ($this->oldquestions as $k => $question) {
            $this->addSql('UPDATE conformite_traitement_question SET position="' . ($k+1) . '" WHERE id="' . $question['id'] . '"');
        }
    }

    public function down(Schema $schema) : void
    {

    }

    private function getData(string $sql): array
    {
        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }
}
