<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210929103247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE aipd_analyse_impact (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aipd_analyse_question_conformite (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', analyse_impact_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', question VARCHAR(255) NOT NULL, is_justification_obligatoire TINYINT(1) NOT NULL, texte_conformite VARCHAR(255) DEFAULT NULL, texte_non_conformite_majeure VARCHAR(255) DEFAULT NULL, texte_non_conformite_mineure VARCHAR(255) DEFAULT NULL, INDEX IDX_BF8E57E498AFC31 (analyse_impact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aipd_analyse_question_conformite ADD CONSTRAINT FK_BF8E57E498AFC31 FOREIGN KEY (analyse_impact_id) REFERENCES aipd_analyse_impact (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE aipd_analyse_question_conformite DROP FOREIGN KEY FK_BF8E57E498AFC31');
        $this->addSql('DROP TABLE aipd_analyse_impact');
        $this->addSql('DROP TABLE aipd_analyse_question_conformite');
    }
}
