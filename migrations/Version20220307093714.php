<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220307093714 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $data = [
            [
                'question'                      => "Exercice du droit à la portabilité",
                'is_justification_obligatoire'  => 0,
                'texte_conformite'              => "Conforme",
                'texte_non_conformite_majeure'  => "Non-conformite majeure",
                'texte_non_conformite_mineure'  => "Non-conforme mineure",
                'position'                      => 13,
            ],
            [
                'question'                      => "Exercice du droit d'effacement",
                'is_justification_obligatoire'  => 0,
                'texte_conformite'              => "Conforme",
                'texte_non_conformite_majeure'  => "Non-conformite majeure",
                'texte_non_conformite_mineure'  => "Non-conforme mineure",
                'position'                      => 14,
            ],
            [
                'question'                      => "Exercice du droit d'opposition",
                'is_justification_obligatoire'  => 0,
                'texte_conformite'              => "Conforme",
                'texte_non_conformite_majeure'  => "Non-conformite majeure",
                'texte_non_conformite_mineure'  => "Non-conforme mineure",
                'position'                      => 15,
            ],

        ];
        foreach ($data as $item) {
            $this->addSql("INSERT INTO conformite_traitement_question(id, question, position) VALUES (?, ?, ?)", [Uuid::uuid4(), $item['question'], $item['position']]);
        }
        foreach ($data as $item) {
            $this->addSql("INSERT INTO aipd_modele_question_conformite(id, question, is_justification_obligatoire, texte_conformite, texte_non_conformite_majeure, texte_non_conformite_mineure, position) VALUES (?, ?, ?, ?, ?, ?, ?)", [Uuid::uuid4(), $item['question'], $item['is_justification_obligatoire'], $item['texte_conformite'], $item['texte_non_conformite_majeure'], $item['texte_non_conformite_mineure'], $item['position'] ]);
        }

        $this->addSql('UPDATE conformite_traitement_question SET question = "Exercice du droit de limitation" WHERE question = "Exercice des droits de limitation du traitement et d\'opposition"');
        $this->addSql('UPDATE conformite_traitement_question SET question = "Exercice du droit de rectification" WHERE question = "Exercice des droits de rectification et d\'effacement"');
        $this->addSql('UPDATE conformite_traitement_question SET question = "Exercice du droit d\'accès" WHERE question = "Exercice des droits d\'accès et à la portabilité"');

        $this->addSql('UPDATE aipd_modele_question_conformite SET question = "Exercice du droit de limitation" WHERE question = "Exercice des droits de limitation du traitement et d\'opposition"');
        $this->addSql('UPDATE aipd_modele_question_conformite SET question = "Exercice du droit de rectification" WHERE question = "Exercice des droits de rectification et d\'effacement"');
        $this->addSql('UPDATE aipd_modele_question_conformite SET question = "Exercice du droit d\'accès" WHERE question = "Exercice des droits d\'accès et à la portabilité"');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
