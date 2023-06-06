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
final class Version20230602065460 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Récuperation des reponses de chaque question de chaque survey
        $answers = $this->connection->query('SELECT * FROM maturity_answer WHERE `survey_id` IS NOT NULL')->fetchAll();
        foreach($answers as $answer){
            $answerSurvey = $this->connection->query('SELECT * FROM maturity_answer WHERE `response` = '. $answer["response"] .' AND `question_id` = "'.$answer["question_id"] .'" AND `name` <> ""')->fetchAll();
            $this->addSql('INSERT INTO answer_survey ( answer_id, survey_id ) VALUES (?,?)',[$answerSurvey[0]['id'], $answer['survey_id']]);
        }

        $this->addSql('DELETE from maturity_answer where `survey_id` IS NOT NULL');
        $this->addSql('ALTER TABLE maturity_answer DROP FOREIGN KEY FK_95FB1493B3FE509D');
        $this->addSql('DROP INDEX IDX_95FB1493B3FE509D ON maturity_answer');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE maturity_answer ADD CONSTRAINT FK_95FB1493B3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_95FB1493B3FE509D ON maturity_answer (survey_id)');
    }
}
