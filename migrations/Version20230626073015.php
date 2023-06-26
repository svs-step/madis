<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230626073015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set conformite traitement questions order for AIPD';
    }

    public function preUp(Schema $schema): void
    {
        parent::preUp($schema);
        $this->oldquestions = $this->getData('SELECT * FROM conformite_traitement_question ORDER BY position ASC');
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        foreach ($this->oldquestions as $k => $question) {
            $this->addSql('UPDATE aipd_modele_question_conformite SET position="' . $question['position'] . '" WHERE question="' . $question['question'] . '"');
            $this->addSql('UPDATE aipd_analyse_question_conformite SET position="' . $question['position'] . '" WHERE question="' . $question['question'] . '"');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    private function getData(string $sql): array
    {
        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }
}
