<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719093804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answer_survey ADD id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('UPDATE answer_survey SET id = UUID()');
        $this->addSql('ALTER TABLE answer_survey DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE answer_survey ADD PRIMARY KEY (id)');
        $this->addSql('CREATE TABLE mesurement_answer_survey (mesurement_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', answer_survey_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_DDDA26792EA38913 (mesurement_id), INDEX IDX_DDDA26798E018F43 (answer_survey_id), PRIMARY KEY(mesurement_id, answer_survey_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mesurement_answer_survey ADD CONSTRAINT FK_DDDA26792EA38913 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mesurement_answer_survey ADD CONSTRAINT FK_DDDA26798E018F43 FOREIGN KEY (answer_survey_id) REFERENCES answer_survey (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mesurement_answer_survey');
    }
}
