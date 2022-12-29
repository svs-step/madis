<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221229142243 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE referentiel (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referentiel_answer (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', referentiel_question_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, recommendation VARCHAR(255) NOT NULL, option_not_concerned TINYINT(1) NOT NULL, INDEX IDX_A20038B0A4406355 (referentiel_question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referentiel_question (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', referentiel_section_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, weight INT NOT NULL, INDEX IDX_4CA5FDC6F800C8E (referentiel_section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referentiel_section (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', referentiel_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_AD6EC056805DB139 (referentiel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE referentiel_answer ADD CONSTRAINT FK_A20038B0A4406355 FOREIGN KEY (referentiel_question_id) REFERENCES referentiel_question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel_question ADD CONSTRAINT FK_4CA5FDC6F800C8E FOREIGN KEY (referentiel_section_id) REFERENCES referentiel_section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel_section ADD CONSTRAINT FK_AD6EC056805DB139 FOREIGN KEY (referentiel_id) REFERENCES referentiel (id) ON DELETE CASCADE');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE referentiel_section DROP FOREIGN KEY FK_AD6EC056805DB139');
        $this->addSql('ALTER TABLE referentiel_answer DROP FOREIGN KEY FK_A20038B0A4406355');
        $this->addSql('ALTER TABLE referentiel_question DROP FOREIGN KEY FK_4CA5FDC6F800C8E');
        $this->addSql('DROP TABLE referentiel');
        $this->addSql('DROP TABLE referentiel_answer');
        $this->addSql('DROP TABLE referentiel_question');
        $this->addSql('DROP TABLE referentiel_section');
    }
}
