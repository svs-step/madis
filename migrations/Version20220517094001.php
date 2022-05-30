<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220517094001 extends AbstractMigration
{
    private $question1;
    private $question2;
    private $question3;
    private $lastQuestionPosition;

    private $modelesAipd;

    private function getData(string $sql): array
    {
        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }

    public function preUp(Schema $schema): void
    {
        //Set position to int
        $this->connection->query('ALTER TABLE aipd_analyse_question_conformite MODIFY position INT UNSIGNED NOT NULL;');
        $this->connection->query('ALTER TABLE aipd_modele_question_conformite MODIFY position INT UNSIGNED NOT NULL;');
        // set new position for all old questions
        $this->connection->query('update aipd_modele_question_conformite set position=position*10 WHERE position < 100');
        $this->connection->query('update aipd_analyse_question_conformite set position=position*10 WHERE position < 100');

        // Get existing questions to be duplicated
        $this->question1 = $this->getData('SELECT * FROM aipd_analyse_question_conformite WHERE question = "Exercice des droits d\'accès et à la portabilité"');
        $this->question2 = $this->getData('SELECT * FROM aipd_analyse_question_conformite WHERE question = "Exercice des droits de rectification et d\'effacement"');
        $this->question3 = $this->getData('SELECT * FROM aipd_analyse_question_conformite WHERE question = "Exercice des droits de limitation du traitement et d\'opposition"');

        $p1 = count($this->question1) ? $this->question1[0]['position'] : 0;
        $p2 = count($this->question2) ? $this->question2[0]['position'] : 0;
        $p3 = count($this->question3) ? $this->question3[0]['position'] : 0;
        $this->lastQuestionPosition = max($p1, $p2, $p3);

        $this->modelesAipd = $this->getData('SELECT * FROM aipd_modele');
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE aipd_analyse_question_conformite SET question = "Exercice du droit de limitation" WHERE question = "Exercice des droits de limitation du traitement et d\'opposition"');
        $this->addSql('UPDATE aipd_analyse_question_conformite SET question = "Exercice du droit de rectification" WHERE question = "Exercice des droits de rectification et d\'effacement"');
        $this->addSql('UPDATE aipd_analyse_question_conformite SET question = "Exercice du droit d\'accès" WHERE question = "Exercice des droits d\'accès et à la portabilité"');

        $data = [
            [
                'question'                      => 'Exercice du droit à la portabilité',
                'is_justification_obligatoire'  => 0,
                'texte_conformite'              => 'Conforme',
                'texte_non_conformite_majeure'  => 'Non-conformite majeure',
                'texte_non_conformite_mineure'  => 'Non-conforme mineure',
                'position'                      => $this->lastQuestionPosition + 3,
            ],
            [
                'question'                      => "Exercice du droit d'effacement",
                'is_justification_obligatoire'  => 0,
                'texte_conformite'              => 'Conforme',
                'texte_non_conformite_majeure'  => 'Non-conformite majeure',
                'texte_non_conformite_mineure'  => 'Non-conforme mineure',
                'position'                      => $this->lastQuestionPosition + 6,
            ],
            [
                'question'                      => "Exercice du droit d'opposition",
                'is_justification_obligatoire'  => 0,
                'texte_conformite'              => 'Conforme',
                'texte_non_conformite_majeure'  => 'Non-conformite majeure',
                'texte_non_conformite_mineure'  => 'Non-conforme mineure',
                'position'                      => $this->lastQuestionPosition + 9,
            ],
        ];

        foreach ($data as $k => $item) {
            // ADD to analyse
            foreach ($this->{'question' . ($k + 1)} as $q) {
                $this->addSql('INSERT INTO aipd_analyse_question_conformite(id, analyse_impact_id, question, is_justification_obligatoire, texte_conformite, texte_non_conformite_majeure, texte_non_conformite_mineure, position, justificatif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    Uuid::uuid4()->__toString(),
                    $q['analyse_impact_id'],
                    $item['question'],
                    $q['is_justification_obligatoire'],
                    $q['texte_conformite'],
                    $q['texte_non_conformite_majeure'],
                    $q['texte_non_conformite_mineure'],
                    $item['position'],
                    $q['justificatif'],
                ]);
            }

            // Add to models
            foreach ($this->modelesAipd as $model) {
                $this->addSql('INSERT INTO aipd_modele_question_conformite(id, modele_analyse_id, question, is_justification_obligatoire, texte_conformite, texte_non_conformite_majeure, texte_non_conformite_mineure, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
                    Uuid::uuid4()->__toString(),
                    $model['id'],
                    $item['question'],
                    $item['is_justification_obligatoire'],
                    $item['texte_conformite'],
                    $item['texte_non_conformite_majeure'],
                    $item['texte_non_conformite_mineure'],
                    $item['position'],
                ]);
            }
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
