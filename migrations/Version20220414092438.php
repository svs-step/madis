<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220414092438 extends AbstractMigration
{
    private $question1;
    private $question2;
    private $question3;
    private $answer1 = [];
    private $answer2 = [];
    private $answer3 = [];

    private $ids;
    private $ids2;

    public function getDescription(): string
    {
        return '';
    }

    public function preUp(Schema $schema): void
    {
        // FIND IDs FROM OLD QUESTIONS
        $this->question1 = $this->getData('SELECT id FROM conformite_traitement_question WHERE question = "Exercice des droits d\'accès et à la portabilité"');
        $this->question2 = $this->getData('SELECT id FROM conformite_traitement_question WHERE question = "Exercice des droits de rectification et d\'effacement"');
        $this->question3 = $this->getData('SELECT id FROM conformite_traitement_question WHERE question = "Exercice des droits de limitation du traitement et d\'opposition"');
        // FIND ANSWERS FROM THOSE QUESTIONS
        if ($this->question1) {
            $this->answer1 = $this->getData('SELECT * FROM conformite_traitement_reponse WHERE question_id = "' . $this->question1[0]['id'] . '"');
        }
        if ($this->question2) {
            $this->answer2 = $this->getData('SELECT * FROM conformite_traitement_reponse WHERE question_id = "' . $this->question2[0]['id'] . '"');
        }
        if ($this->question3) {
            $this->answer3 = $this->getData('SELECT * FROM conformite_traitement_reponse WHERE question_id = "' . $this->question3[0]['id'] . '"');
        }

        foreach ($this->answer1 as $k => $answer) {
            $this->answer1[$k]['actionProtections']        = $this->getData('SELECT * FROM conformite_traitement_reponse_action_protection WHERE reponse_id = "' . $answer['id'] . '"');
            $this->answer1[$k]['actionProtectionsNotSeen'] = $this->getData('SELECT * FROM conformite_traitement_reponse_action_protection_not_seen WHERE reponse_id = "' . $answer['id'] . '"');
        }
        foreach ($this->answer2 as $k => $answer) {
            $this->answer2[$k]['actionProtections']        = $this->getData('SELECT * FROM conformite_traitement_reponse_action_protection WHERE reponse_id = "' . $answer['id'] . '"');
            $this->answer2[$k]['actionProtectionsNotSeen'] = $this->getData('SELECT * FROM conformite_traitement_reponse_action_protection_not_seen WHERE reponse_id = "' . $answer['id'] . '"');
        }
        foreach ($this->answer3 as $k => $answer) {
            $this->answer3[$k]['actionProtections']        = $this->getData('SELECT * FROM conformite_traitement_reponse_action_protection WHERE reponse_id = "' . $answer['id'] . '"');
            $this->answer3[$k]['actionProtectionsNotSeen'] = $this->getData('SELECT * FROM conformite_traitement_reponse_action_protection_not_seen WHERE reponse_id = "' . $answer['id'] . '"');
        }

        //Generate ids for new questions
        $this->ids = [
            Uuid::uuid4()->__toString(),
            Uuid::uuid4()->__toString(),
            Uuid::uuid4()->__toString(),
        ];

        $this->ids2 = [
            Uuid::uuid4()->__toString(),
            Uuid::uuid4()->__toString(),
            Uuid::uuid4()->__toString(),
        ];

        // DUPLICATE ANSWER & APPLY TO NEW QUESTIONS
    }

    private function getData(string $sql): array
    {
        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }

    public function postUp(Schema $schema): void
    {
        //Insert responses to new questions

        foreach ($this->answer1 as $answer) {
            $answerId = Uuid::uuid4()->__toString();
            $data     = [
                'id'                       => $answerId,
                'question_id'              => $this->ids[0],
                'conformite_traitement_id' => $answer['conformite_traitement_id'],
                'conforme'                 => $answer['conforme'],
            ];
            $this->connection->insert('conformite_traitement_reponse', $data);

            foreach ($answer['actionProtections'] as $action) {
                $data2 = [
                    'reponse_id'    => $answerId,
                    'mesurement_id' => $action['mesurement_id'],
                ];
                $this->connection->insert('conformite_traitement_reponse_action_protection', $data2);
            }
            foreach ($answer['actionProtectionsNotSeen'] as $action) {
                $data2 = [
                    'reponse_id'    => $answerId,
                    'mesurement_id' => $action['mesurement_id'],
                ];
                $this->connection->insert('conformite_traitement_reponse_action_protection_not_seen', $data2);
            }
        }
        foreach ($this->answer2 as $answer) {
            $answerId = Uuid::uuid4()->__toString();
            $data     = [
                'id'                       => $answerId,
                'question_id'              => $this->ids[1],
                'conformite_traitement_id' => $answer['conformite_traitement_id'],
                'conforme'                 => $answer['conforme'],
            ];
            $this->connection->insert('conformite_traitement_reponse', $data);

            foreach ($answer['actionProtections'] as $action) {
                $data2 = [
                    'reponse_id'    => $answerId,
                    'mesurement_id' => $action['mesurement_id'],
                ];
                $this->connection->insert('conformite_traitement_reponse_action_protection', $data2);
            }
            foreach ($answer['actionProtectionsNotSeen'] as $action) {
                $data2 = [
                    'reponse_id'    => $answerId,
                    'mesurement_id' => $action['mesurement_id'],
                ];
                $this->connection->insert('conformite_traitement_reponse_action_protection_not_seen', $data2);
            }
        }
        foreach ($this->answer3 as $answer) {
            $answerId = Uuid::uuid4()->__toString();
            $data     = [
                'id'                       => $answerId,
                'question_id'              => $this->ids[2],
                'conformite_traitement_id' => $answer['conformite_traitement_id'],
                'conforme'                 => $answer['conforme'],
            ];
            $this->connection->insert('conformite_traitement_reponse', $data);
            foreach ($answer['actionProtections'] as $action) {
                $data2 = [
                    'reponse_id'    => $answerId,
                    'mesurement_id' => $action['mesurement_id'],
                ];
                $this->connection->insert('conformite_traitement_reponse_action_protection', $data2);
            }
            foreach ($answer['actionProtectionsNotSeen'] as $action) {
                $data2 = [
                    'reponse_id'    => $answerId,
                    'mesurement_id' => $action['mesurement_id'],
                ];
                $this->connection->insert('conformite_traitement_reponse_action_protection_not_seen', $data2);
            }
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $data = [
            [
                'question'                      => 'Exercice du droit à la portabilité',
                'is_justification_obligatoire'  => 0,
                'texte_conformite'              => 'Conforme',
                'texte_non_conformite_majeure'  => 'Non-conformite majeure',
                'texte_non_conformite_mineure'  => 'Non-conforme mineure',
                'position'                      => 13,
            ],
            [
                'question'                      => "Exercice du droit d'effacement",
                'is_justification_obligatoire'  => 0,
                'texte_conformite'              => 'Conforme',
                'texte_non_conformite_majeure'  => 'Non-conformite majeure',
                'texte_non_conformite_mineure'  => 'Non-conforme mineure',
                'position'                      => 14,
            ],
            [
                'question'                      => "Exercice du droit d'opposition",
                'is_justification_obligatoire'  => 0,
                'texte_conformite'              => 'Conforme',
                'texte_non_conformite_majeure'  => 'Non-conformite majeure',
                'texte_non_conformite_mineure'  => 'Non-conforme mineure',
                'position'                      => 15,
            ],
        ];
        foreach ($data as $k => $item) {
            $this->addSql('INSERT INTO conformite_traitement_question(id, question, position) VALUES (?, ?, ?)', [$this->ids[$k], $item['question'], $item['position']]);
        }
        foreach ($data as $k => $item) {
            $this->addSql('INSERT INTO aipd_modele_question_conformite(id, question, is_justification_obligatoire, texte_conformite, texte_non_conformite_majeure, texte_non_conformite_mineure, position) VALUES (?, ?, ?, ?, ?, ?, ?)', [$this->ids2[$k], $item['question'], $item['is_justification_obligatoire'], $item['texte_conformite'], $item['texte_non_conformite_majeure'], $item['texte_non_conformite_mineure'], $item['position']]);
        }

        $this->addSql('UPDATE conformite_traitement_question SET question = "Exercice du droit de limitation" WHERE question = "Exercice des droits de limitation du traitement et d\'opposition"');
        $this->addSql('UPDATE conformite_traitement_question SET question = "Exercice du droit de rectification" WHERE question = "Exercice des droits de rectification et d\'effacement"');
        $this->addSql('UPDATE conformite_traitement_question SET question = "Exercice du droit d\'accès" WHERE question = "Exercice des droits d\'accès et à la portabilité"');

        $this->addSql('UPDATE aipd_modele_question_conformite SET question = "Exercice du droit de limitation" WHERE question = "Exercice des droits de limitation du traitement et d\'opposition"');
        $this->addSql('UPDATE aipd_modele_question_conformite SET question = "Exercice du droit de rectification" WHERE question = "Exercice des droits de rectification et d\'effacement"');
        $this->addSql('UPDATE aipd_modele_question_conformite SET question = "Exercice du droit d\'accès" WHERE question = "Exercice des droits d\'accès et à la portabilité"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
