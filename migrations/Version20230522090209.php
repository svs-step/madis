<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522090209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE maturity_optional_answer (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', question_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', survey_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', reason LONGTEXT NOT NULL, INDEX IDX_B3D19E5D1E27F6BF (question_id), INDEX IDX_B3D19E5DB3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE maturity_optional_answer ADD CONSTRAINT FK_B3D19E5D1E27F6BF FOREIGN KEY (question_id) REFERENCES maturity_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maturity_optional_answer ADD CONSTRAINT FK_B3D19E5DB3FE509D FOREIGN KEY (survey_id) REFERENCES maturity_survey (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE maturity_optional_answer');
    }
}
