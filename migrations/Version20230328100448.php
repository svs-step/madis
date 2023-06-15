<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230328100448 extends AbstractMigration
{
    protected $oldquestions;
    protected $newquestions;

    public function getDescription(): string
    {
        return 'Remove extra questions conformité';
    }

    public function preUp(Schema $schema): void
    {
        parent::preUp($schema);
        $this->oldquestions = $this->getData('SELECT * FROM conformite_traitement_question WHERE position IN (13, 14, 15)');
        $this->newquestions = $this->getData('SELECT * FROM conformite_traitement_question WHERE position IN (103, 106, 109)');
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        if (3 === count($this->newquestions) && 3 === count($this->oldquestions)) {
            $this->addSql('UPDATE conformite_traitement_reponse SET question_id="' . $this->newquestions[0]['id'] . '" WHERE question_id="' . $this->oldquestions[0]['id'] . '"');
            $this->addSql('UPDATE conformite_traitement_reponse SET question_id="' . $this->newquestions[1]['id'] . '" WHERE question_id="' . $this->oldquestions[1]['id'] . '"');
            $this->addSql('UPDATE conformite_traitement_reponse SET question_id="' . $this->newquestions[2]['id'] . '" WHERE question_id="' . $this->oldquestions[2]['id'] . '"');
            $this->addSql('DELETE FROM conformite_traitement_question WHERE position IN (13, 14, 15)');
        }
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }

    private function getData(string $sql): array
    {
        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }
}
