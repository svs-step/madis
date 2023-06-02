<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Domain\Maturity\Model\Question;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use App\Domain\Maturity\Repository;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602065459 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE maturity_question SET weight = 1 WHERE weight = 0');

        $this->addSql('DELETE from maturity_answer');
        $questions = $this->connection->query('SELECT `id` FROM maturity_question')->fetchAll();
        $uniqueQuestions =[];
        foreach ($questions as $question){
            if (!in_array($question['id'], $uniqueQuestions)){
                $uniqueQuestions[] = $question['id'];
            }
        }

        foreach ($uniqueQuestions as $question_id) {
            $this->addSql('INSERT INTO maturity_answer (id, question_id, response, recommendation, name, position) VALUES (?,?,?,?,?,?)',[Uuid::uuid4(), $question_id, 0, '', 'Non / Je ne sais pas', 0]);
            $this->addSql('INSERT INTO maturity_answer (id, question_id, response, recommendation, name, position) VALUES (?,?,?,?,?,?)',[Uuid::uuid4(), $question_id, 1, '', 'En partie', 1]);
            $this->addSql('INSERT INTO maturity_answer (id, question_id, response, recommendation, name, position) VALUES (?,?,?,?,?,?)',[Uuid::uuid4(), $question_id, 2, '', 'Oui / Complètement', 2]);
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
